<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('filieres', AdminController::class)->except(['show']);

        Route::get('demandes', [AdminController::class, 'demandes'])->name('demandes.index');
        Route::patch('demandes/{demande}/traiter', [AdminController::class, 'traiterDemande'])->name('demandes.traiter');
    });