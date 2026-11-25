<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('filieres', AdminController::class)->except(['show']);

        Route::get('memoires', [AdminController::class, 'memoires'])->name('memoires.index');
        Route::patch('memoires/{memoire}/valider', [AdminController::class, 'validerMemoire'])->name('memoires.valider');
        Route::patch('memoires/{memoire}/rejeter', [AdminController::class, 'rejeterMemoire'])->name('memoires.rejeter');
    });