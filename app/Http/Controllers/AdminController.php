<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $filieres = Filiere::orderBy('nom')->get();

        return view('admin.filieres.liste', compact('filieres'));
    }

    public function create(): View
    {
        return view('admin.filieres.creer');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:filieres,code'],
            'description' => ['nullable', 'string'],
        ]);

        Filiere::create($data);

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière créée.');
    }

    public function edit(Filiere $filiere): View
    {
        return view('admin.filieres.modifier', compact('filiere'));
    }

    public function update(Request $request, Filiere $filiere): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('filieres', 'code')->ignore($filiere->id)],
            'description' => ['nullable', 'string'],
        ]);

        $filiere->update($data);

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière modifiée.');
    }

    public function destroy(Filiere $filiere): RedirectResponse
    {
        if ($filiere->memoires()->exists()) {
            return back()->with('error', 'Cette filière contient des mémoires, suppression impossible.');
        }

        $filiere->delete();

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière supprimée.');
    }
}