<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ValidationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // Dashboard et actions du validateur
    Route::middleware('role:validateur,admin')
        ->prefix('validation')
        ->name('validation.')
        ->group(function () {
            Route::get('/', [ValidationController::class, 'index'])->name('index');
            Route::get('/memoires/{memoire}', [ValidationController::class, 'show'])->name('show');
            Route::post('/memoires/{memoire}/statuer', [ValidationController::class, 'statuer'])->name('statuer');
            Route::patch('/memoires/{memoire}/telechargement', [ValidationController::class, 'telechargement'])->name('telechargement');
        });

    // Notifications (tout utilisateur connecté, surtout les étudiants)
    Route::prefix('notifications')
        ->name('validation.notifications.')
        ->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::patch('/{notificationId}/lue', [NotificationController::class, 'marquerLue'])->name('lue');
            Route::patch('/tout-lire', [NotificationController::class, 'toutMarquerLu'])->name('tout-lire');
        });
});
