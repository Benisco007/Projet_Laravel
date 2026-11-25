<?php

use App\Http\Controllers\LecteurController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('catalogue')
    ->name('lecteur.')
    ->group(function () {
        Route::get('/', [LecteurController::class, 'catalogue'])->name('catalogue');
        Route::get('/favoris', [LecteurController::class, 'favoris'])->name('favoris');
        Route::get('/historique', [LecteurController::class, 'historique'])->name('historique');

        Route::get('/{memoire}', [LecteurController::class, 'detail'])->name('detail')->whereNumber('memoire');
        Route::get('/{memoire}/lire', [LecteurController::class, 'lire'])->name('lire')->whereNumber('memoire');
        Route::get('/{memoire}/pdf', [LecteurController::class, 'pdf'])->name('pdf')->whereNumber('memoire');
        Route::post('/{memoire}/favori', [LecteurController::class, 'basculerFavori'])->name('favori')->whereNumber('memoire');

        // Réservé aux externes : obtenir le mémoire complet (achat simulé)
        Route::middleware('role:externe')->group(function () {
            Route::get('/{memoire}/obtenir', [LecteurController::class, 'obtenir'])->name('obtenir')->whereNumber('memoire');
            Route::get('/{memoire}/paiement', [LecteurController::class, 'paiement'])->name('paiement')->whereNumber('memoire');
            Route::post('/{memoire}/paiement', [LecteurController::class, 'payer'])->name('payer')->whereNumber('memoire');
            Route::get('/{memoire}/recu/{demande}', [LecteurController::class, 'recu'])->name('recu')
                ->whereNumber('memoire')->whereNumber('demande');
        });
    });