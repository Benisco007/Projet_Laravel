<?php

namespace App\Http\Controllers;

use App\Models\DemandeAchat;
use App\Models\Favori;
use App\Models\Filiere;
use App\Models\HistoriqueLecture;
use App\Models\Memoire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LecteurController extends Controller
{
    // ---------- Catalogue ----------

    public function catalogue(Request $request): View
    {
        $recherche = trim((string) $request->query('recherche', ''));
        $filiereId = $request->integer('filiere');

        $memoires = $this->filtreVisible(Memoire::query())
            ->with(['filiere', 'etudiant'])
            ->when($recherche !== '', function (Builder $q) use ($recherche) {
                $q->where(function (Builder $q2) use ($recherche) {
                    $q2->where('titre', 'like', "%{$recherche}%")
                        ->orWhere('mots_cles', 'like', "%{$recherche}%")
                        ->orWhere('resume', 'like', "%{$recherche}%");
                });
            })
            ->when($filiereId, fn (Builder $q) => $q->where('filiere_id', $filiereId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $filieres = Filiere::orderBy('nom')->get();

        return view('lecteur.catalogue', compact('memoires', 'filieres', 'recherche', 'filiereId'));
    }

    public function detail(Memoire $memoire): View
    {
        $this->verifierVisible($memoire);

        $memoire->load(['filiere', 'etudiant', 'encadreur']);

        $estFavori = Favori::where('user_id', auth()->id())
            ->where('memoire_id', $memoire->id)
            ->exists();
        $accesComplet = $this->aAccesComplet($memoire);
        $limitePages = $this->limitePages($memoire);

        return view('lecteur.detail', compact('memoire', 'estFavori', 'accesComplet', 'limitePages'));
    }

    // ---------- Lecture du PDF ----------

    public function lire(Memoire $memoire): View
    {
        $this->verifierVisible($memoire);
        $this->enregistrerLecture($memoire);

        $limitePages = $this->limitePages($memoire);

        return view('lecteur.lecture', compact('memoire', 'limitePages'));
    }

    /**
     * Envoie le fichier PDF au lecteur PDF.js.
     * Le fichier passe par ce contrôleur : il n'est servi qu'aux utilisateurs
     * connectés, et seulement si le mémoire est validé et hors embargo.
     */
    public function pdf(Memoire $memoire): Response
    {
        $this->verifierVisible($memoire);

        $disque = Storage::disk(config('memoire.storage_disk', 'public'));
        abort_unless($disque->exists($memoire->fichier_pdf), 404, 'Fichier introuvable.');

        return $disque->response($memoire->fichier_pdf, null, ['Content-Type' => 'application/pdf']);
    }

    // ---------- Favoris ----------

    public function favoris(): View
    {
        $memoires = $this->filtreVisible(Memoire::query())
            ->with(['filiere', 'etudiant'])
            ->whereIn('id', Favori::where('user_id', auth()->id())->select('memoire_id'))
            ->latest()
            ->paginate(12);

        return view('lecteur.favoris', compact('memoires'));
    }

    public function basculerFavori(Memoire $memoire): RedirectResponse
    {
        $this->verifierVisible($memoire);

        $retires = Favori::where('user_id', auth()->id())
            ->where('memoire_id', $memoire->id)
            ->delete();

        if ($retires > 0) {
            return back()->with('success', 'Mémoire retiré de vos favoris.');
        }

        Favori::firstOrCreate([
            'user_id' => auth()->id(),
            'memoire_id' => $memoire->id,
        ]);

        return back()->with('success', 'Mémoire ajouté à vos favoris.');
    }

    // ---------- Historique ----------

    public function historique(): View
    {
        $lectures = HistoriqueLecture::with('memoire.filiere')
            ->where('user_id', auth()->id())
            ->whereHas('memoire', fn (Builder $q) => $this->filtreVisible($q))
            ->orderByDesc('date_lecture')
            ->orderByDesc('id')
            ->paginate(15);

        return view('lecteur.historique', compact('lectures'));
    }

    // ---------- Obtenir le mémoire complet (externes) : achat simulé ----------

    /** Étape 1 : choix de l'option. */
    public function obtenir(Memoire $memoire): View
    {
        $this->verifierVisible($memoire);

        return view('lecteur.obtenir', [
            'memoire' => $memoire,
            'modes' => DemandeAchat::MODES,
            'prix' => DemandeAchat::PRIX,
        ]);
    }

    /** Étape 2 : page de (faux) paiement pour l'option choisie. */
    public function paiement(Request $request, Memoire $memoire): View|RedirectResponse
    {
        $this->verifierVisible($memoire);

        $mode = $request->query('mode');

        if (! is_string($mode) || ! array_key_exists($mode, DemandeAchat::MODES)) {
            return redirect()->route('lecteur.obtenir', $memoire)
                ->with('error', 'Choisissez une option pour continuer.');
        }

        if ($mode === 'en_ligne' && $this->aAccesComplet($memoire)) {
            return redirect()->route('lecteur.detail', $memoire)
                ->with('error', 'Vous avez déjà acheté la version numérique de ce mémoire.');
        }

        return view('lecteur.paiement', [
            'memoire' => $memoire,
            'mode' => $mode,
            'libelleMode' => DemandeAchat::MODES[$mode],
            'montant' => DemandeAchat::PRIX[$mode],
            'moyens' => DemandeAchat::MOYENS,
        ]);
    }

    /** Étape 3 : valide le paiement simulé et enregistre la demande. */
    public function payer(Request $request, Memoire $memoire): RedirectResponse
    {
        $this->verifierVisible($memoire);

        $data = $request->validate([
            'mode' => ['required', Rule::in(array_keys(DemandeAchat::MODES))],
        ]);
        $mode = $data['mode'];
        $montant = DemandeAchat::PRIX[$mode];

        if ($mode === 'en_ligne' && $this->aAccesComplet($memoire)) {
            return redirect()->route('lecteur.detail', $memoire)
                ->with('error', 'Vous avez déjà acheté la version numérique de ce mémoire.');
        }

        $dejaEnvoyee = DemandeAchat::where('user_id', auth()->id())
            ->where('memoire_id', $memoire->id)
            ->where('mode', $mode)
            ->where('statut', 'nouvelle')
            ->exists();

        if ($dejaEnvoyee) {
            return redirect()->route('lecteur.detail', $memoire)
                ->with('error', 'Vous avez déjà envoyé cette demande, elle est en cours de traitement.');
        }

        if ($montant > 0) {
            // Le numéro n'est volontairement PAS enregistré : c'est une simulation.
            $request->validate([
                'moyen' => ['required', Rule::in(array_keys(DemandeAchat::MOYENS))],
                'numero' => ['required', 'regex:/^[0-9 +]{6,20}$/'],
            ], [
                'numero.regex' => 'Entrez un numéro de téléphone (chiffres seulement).',
            ]);
        }

        $demande = DemandeAchat::create([
            'user_id' => auth()->id(),
            'memoire_id' => $memoire->id,
            'mode' => $mode,
            'montant' => $montant,
            'reference' => 'SIM-' . strtoupper(Str::random(8)),
            'paye_le' => $montant > 0 ? now() : null,
        ]);

        return redirect()->route('lecteur.recu', [$memoire, $demande]);
    }

    /** Étape 4 : reçu. */
    public function recu(Memoire $memoire, DemandeAchat $demande): View
    {
        abort_unless(
            $demande->user_id === auth()->id() && $demande->memoire_id === $memoire->id,
            404
        );

        return view('lecteur.recu', compact('memoire', 'demande'));
    }

    // ---------- Outils internes ----------

    /** Un mémoire est visible s'il est validé et hors embargo. */
    private function filtreVisible(Builder $query): Builder
    {
        return $query->where('statut', 'valide')
            ->where(function (Builder $q) {
                $q->whereNull('embargo_date')->orWhere('embargo_date', '<=', today());
            });
    }

    private function verifierVisible(Memoire $memoire): void
    {
        abort_unless($memoire->estVisible(), 404);
    }

    /** Vrai si l'utilisateur a "payé" (simulation) la version numérique de ce mémoire. */
    private function aAccesComplet(Memoire $memoire): bool
    {
        return DemandeAchat::where('user_id', auth()->id())
            ->where('memoire_id', $memoire->id)
            ->where('mode', 'en_ligne')
            ->whereNotNull('paye_le')
            ->exists();
    }

    /** Pages lisibles : limitées pour l'externe qui n'a pas acheté, illimitées (null) sinon. */
    private function limitePages(Memoire $memoire): ?int
    {
        if (auth()->user()->role !== 'externe' || $this->aAccesComplet($memoire)) {
            return null;
        }

        return (int) config('memoire.pages_preview_externe', 4);
    }

    /** Une ligne par jour, par utilisateur et par mémoire. */
    private function enregistrerLecture(Memoire $memoire): void
    {
        $dejaLu = HistoriqueLecture::where('user_id', auth()->id())
            ->where('memoire_id', $memoire->id)
            ->whereDate('date_lecture', today())
            ->exists();

        if ($dejaLu) {
            return;
        }

        try {
            HistoriqueLecture::create([
                'user_id' => auth()->id(),
                'memoire_id' => $memoire->id,
                'date_lecture' => today(),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Deux requêtes en même temps : la ligne du jour existe déjà, rien à faire.
        }
    }
}