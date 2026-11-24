<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use App\Services\PlagiatService;
use Illuminate\Http\Request;

class MemoireController extends Controller
{
    // Dashboard étudiant
    public function index()
    {
        $memoires = auth()->user()->memoires()->latest()->get();
        return view('etudiant.dashboard', compact('memoires'));
    }

    // Formulaire de dépôt
    public function create()
    {
        $this->authorize('create', Memoire::class);
        $encadreurs = \App\Models\User::where('role', 'encadreur')->get();
        $filieres   = \App\Models\Filiere::all();
        return view('etudiant.deposer', compact('encadreurs', 'filieres'));
    }

    // Enregistrement du dépôt
    public function store(Request $request)
    {
        $this->authorize('create', Memoire::class);

        $request->validate([
            'titre'            => 'required|string|max:255',
            'resume'           => 'required|string',
            'mots_cles'        => 'nullable|string|max:255',
            'annee_academique' => 'required|string|max:9',
            'encadreur_id'     => 'nullable|exists:users,id',
            'filiere_id'       => 'nullable|exists:filieres,id',
            'fichier_pdf'      => 'required|file|mimes:pdf|max:' . (config('memoire.max_pdf_size_mb') * 1024),
        ]);

        $path = $request->file('fichier_pdf')->store(
            config('memoire.storage_path'),
            config('memoire.storage_disk')
        );

        $memoire = Memoire::create([
            'titre'            => $request->titre,
            'resume'           => $request->resume,
            'mots_cles'        => $request->mots_cles,
            'annee_academique' => $request->annee_academique,
            'encadreur_id'     => $request->encadreur_id,
            'filiere_id'       => $request->filiere_id,
            'fichier_pdf'      => $path,
            'etudiant_id'      => auth()->id(),
            'statut'           => 'soumis',
        ]);

        app(PlagiatService::class)->analyser($memoire);

        return redirect()->route('etudiant.dashboard')
            ->with('success', 'Mémoire déposé avec succès !');
    }

    // Détail mémoire étudiant
    public function show(Memoire $memoire)
    {
        $this->authorize('view', $memoire);
        return view('etudiant.show', compact('memoire'));
    }
}
