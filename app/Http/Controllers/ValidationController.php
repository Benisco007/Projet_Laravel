<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use App\Models\Validation;
use App\Notifications\StatutMemoireNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ValidationController extends Controller
{
    /**
     * Transitions autorisées : statut actuel => statuts atteignables.
     */
    private const TRANSITIONS = [
        'soumis' => ['en_verification'],
        'en_verification' => ['valide', 'rejete'],
    ];

    /**
     * Dashboard validateur : mémoires filtrés par statut.
     */
    public function index(Request $request): View
    {
        $statut = $request->query('statut');

        $memoires = Memoire::with(['etudiant', 'filiere'])
            ->when(
                in_array($statut, config('memoire.statuts'), true),
                fn ($requete) => $requete->where('statut', $statut)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $compteurs = Memoire::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return view('validation.index', [
            'memoires' => $memoires,
            'statutActif' => $statut,
            'compteurs' => $compteurs,
        ]);
    }

    /**
     * Détail d'un mémoire avec formulaire de décision et historique.
     */
    public function show(Memoire $memoire): View
    {
        $memoire->load(['etudiant', 'filiere', 'validations.validateur']);

        return view('validation.show', [
            'memoire' => $memoire,
            'transitions' => self::TRANSITIONS[$memoire->statut] ?? [],
        ]);
    }

    /**
     * Change le statut d'un mémoire, enregistre l'historique et notifie l'étudiant.
     */
    public function statuer(Request $request, Memoire $memoire): RedirectResponse
    {
        $donnees = $request->validate([
            'statut' => ['required', Rule::in(['en_verification', 'valide', 'rejete'])],
            'motif_rejet' => ['required_if:statut,rejete', 'nullable', 'string', 'max:2000'],
        ], [
            'motif_rejet.required_if' => 'Le motif du rejet est obligatoire.',
        ]);

        $autorises = self::TRANSITIONS[$memoire->statut] ?? [];

        if (! in_array($donnees['statut'], $autorises, true)) {
            return back()->withErrors([
                'statut' => 'Ce changement de statut n\'est pas autorisé depuis l\'état actuel.',
            ]);
        }

        $statutPrecedent = $memoire->statut;
        $motifRejet = $donnees['statut'] === 'rejete' ? $donnees['motif_rejet'] : null;

        DB::transaction(function () use ($memoire, $donnees, $statutPrecedent, $motifRejet, $request) {
            Validation::create([
                'memoire_id' => $memoire->id,
                'validateur_id' => $request->user()->id,
                'statut_precedent' => $statutPrecedent,
                'statut_nouveau' => $donnees['statut'],
                'motif_rejet' => $motifRejet,
            ]);

            $memoire->update([
                'statut' => $donnees['statut'],
                'motif_rejet' => $motifRejet,
            ]);
        });

        $memoire->etudiant->notify(new StatutMemoireNotification($memoire->fresh()));

        return redirect()
            ->route('validation.show', $memoire)
            ->with('succes', 'Le statut du mémoire a été mis à jour.');
    }

    /**
     * Active ou désactive le téléchargement du PDF pour ce mémoire.
     */
    public function telechargement(Memoire $memoire): RedirectResponse
    {
        $memoire->update([
            'telechargement_autorise' => ! $memoire->telechargement_autorise,
        ]);

        return back()->with(
            'succes',
            $memoire->telechargement_autorise
                ? 'Téléchargement activé pour ce mémoire.'
                : 'Téléchargement désactivé pour ce mémoire.'
        );
    }
}
