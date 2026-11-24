<?php

use App\Http\Controllers\MemoireController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:etudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    Route::get('/dashboard',  [MemoireController::class, 'index'])->name('dashboard');
    Route::get('/deposer',    [MemoireController::class, 'create'])->name('deposer');
    Route::post('/deposer',   [MemoireController::class, 'store'])->name('deposer.store');
    Route::get('/memoire/{memoire}', [MemoireController::class, 'show'])->name('show');
});
