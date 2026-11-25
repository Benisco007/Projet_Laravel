<?php

namespace App\Http\Controllers;

use App\Models\DemandeAchat;
use App\Models\Filiere;
use App\Models\Memoire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const STATUTS = ['en_attente', 'valide', 'rejete'];

    // ---------- Filières ----------

    public function index(): View
    {
        $filieres = Filiere::orderBy('nom')->get();
        return view('admin.filieres.index', compact('filieres'));
    }

    public function create(): View
    {
        return view('admin.filieres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom'  => ['required', 'string', 'max:255', Rule::unique('filieres')],
            'code' => ['required', 'string', 'max:10', Rule::unique('filieres')],
        ]);

        Filiere::create($request->only('nom', 'code'));

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière créée.');
    }

    public function edit(Filiere $filiere): View
    {
        return view('admin.filieres.edit', compact('filiere'));
    }

    public function update(Request $request, Filiere $filiere): RedirectResponse
    {
        $request->validate([
            'nom'  => ['required', 'string', 'max:255', Rule::unique('filieres')->ignore($filiere)],
            'code' => ['required', 'string', 'max:10',  Rule::unique('filieres')->ignore($filiere)],
        ]);

        $filiere->update($request->only('nom', 'code'));

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière modifiée.');
    }

    public function destroy(Filiere $filiere): RedirectResponse
    {
        $filiere->delete();

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière supprimée.');
    }

    // ---------- Validation des mémoires (Mindy) ----------

    public function memoires(Request $request): View
    {
        $statut = in_array($request->query('statut'), self::STATUTS, true)
            ? $request->query('statut')
            : 'en_attente';

        $memoires = Memoire::with('filiere')
            ->where('statut', $statut)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.memoires.index', compact('memoires', 'statut'));
    }

    public function validerMemoire(Memoire $memoire): RedirectResponse
    {
        return $this->changerStatut($memoire, 'valide', 'Mémoire validé.');
    }

    public function rejeterMemoire(Memoire $memoire): RedirectResponse
    {
        return $this->changerStatut($memoire, 'rejete', 'Mémoire rejeté.');
    }

    private function changerStatut(Memoire $memoire, string $statut, string $message): RedirectResponse
    {
        if ($memoire->statut !== 'en_attente') {
            return back()->with('error', 'Ce mémoire a déjà été traité.');
        }

        $memoire->update(['statut' => $statut]);

        return back()->with('success', $message);
    }

    // ---------- Demandes d'achat (Emma) ----------

    public function demandes(): View
    {
        $demandes = DemandeAchat::with(['user', 'memoire'])
            ->orderBy('statut')
            ->latest()
            ->paginate(15);

        return view('admin.demandes.liste', compact('demandes'));
    }

    public function traiterDemande(DemandeAchat $demande): RedirectResponse
    {
        $demande->update(['statut' => 'traitee']);

        return back()->with('success', 'Demande marquée comme traitée.');
    }
}
