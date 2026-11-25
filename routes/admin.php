<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
                Route::resource('filieres', AdminController::class)->except(['show']);

                // Validation des mémoires (Mindy)
                Route::get('memoires', [AdminController::class, 'memoires'])->name('memoires.index');
                Route::patch('memoires/{memoire}/valider', [AdminController::class, 'validerMemoire'])->name('memoires.valider');
                Route::patch('memoires/{memoire}/rejeter', [AdminController::class, 'rejeterMemoire'])->name('memoires.rejeter');

                // Demandes d'achat (Emma)
                Route::get('demandes', [AdminController::class, 'demandes'])->name('demandes.index');
                Route::patch('demandes/{demande}/traiter', [AdminController::class, 'traiterDemande'])->name('demandes.traiter');
        });
