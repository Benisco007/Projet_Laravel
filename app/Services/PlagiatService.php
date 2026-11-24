<?php

namespace App\Services;

use App\Models\Memoire;
use App\Models\Validation;
use App\Notifications\StatutMemoireNotification;
use App\Support\SimilariteTexte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Throwable;

/**
 * Détection de plagiat simplifiée, à appeler après chaque dépôt :
 *     app(\App\Services\PlagiatService::class)->analyser($memoire);
 */
class PlagiatService
{
    /** Seuil de similarité (0 à 1) à partir duquel le mémoire est rejeté automatiquement. */
    public const SEUIL = 0.80;

    private const LIMITE_CARACTERES = 500000;

    /**
     * Extrait le texte, le compare aux mémoires validés et rejette si le seuil est atteint.
     * Retourne le meilleur score de similarité (0 à 1).
     */
    public function analyser(Memoire $memoire): float
    {
        $texte = $this->extraireTexte($memoire);

        $memoire->forceFill(['texte_extrait' => $texte !== '' ? $texte : null])->save();

        // PDF scanné ou illisible : pas de comparaison possible, le validateur décidera.
        if ($texte === '') {
            return 0.0;
        }

        $meilleurScore = 0.0;
        $memoireSimilaire = null;

        Memoire::query()
            ->select(['id', 'titre', 'texte_extrait'])
            ->where('statut', 'valide')
            ->where('id', '!=', $memoire->id)
            ->whereNotNull('texte_extrait')
            ->cursor()
            ->each(function (Memoire $autre) use ($texte, &$meilleurScore, &$memoireSimilaire) {
                $score = SimilariteTexte::cosinus($texte, $autre->texte_extrait);

                if ($score > $meilleurScore) {
                    $meilleurScore = $score;
                    $memoireSimilaire = $autre;
                }
            });

        $memoire->forceFill([
            'score_similarite' => round($meilleurScore * 100, 2),
            'memoire_similaire_id' => $memoireSimilaire?->id,
        ])->save();

        if ($meilleurScore >= self::SEUIL && $memoireSimilaire) {
            $this->rejeterPourPlagiat($memoire, $meilleurScore, $memoireSimilaire);
        }

        return $meilleurScore;
    }

    private function extraireTexte(Memoire $memoire): string
    {
        try {
            $chemin = Storage::disk(config('memoire.storage_disk', 'public'))->path($memoire->fichier_pdf);

            $texte = (new Parser())->parseFile($chemin)->getText();

            return mb_substr(trim($texte), 0, self::LIMITE_CARACTERES);
        } catch (Throwable $exception) {
            report($exception);

            return '';
        }
    }

    private function rejeterPourPlagiat(Memoire $memoire, float $score, Memoire $memoireSimilaire): void
    {
        $motif = sprintf(
            'Plagiat détecté : %d %% de similarité avec le mémoire « %s ».',
            round($score * 100),
            $memoireSimilaire->titre
        );

        $statutPrecedent = $memoire->statut;

        DB::transaction(function () use ($memoire, $statutPrecedent, $motif) {
            // validateur_id reste vide : décision automatique du système.
            Validation::create([
                'memoire_id' => $memoire->id,
                'validateur_id' => null,
                'statut_precedent' => $statutPrecedent,
                'statut_nouveau' => 'rejete',
                'motif_rejet' => $motif,
            ]);

            $memoire->update([
                'statut' => 'rejete',
                'motif_rejet' => $motif,
            ]);
        });

        $memoire->etudiant->notify(new StatutMemoireNotification($memoire->fresh()));
    }
}
