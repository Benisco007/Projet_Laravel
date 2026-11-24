<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Memoire extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'resume',
        'mots_cles',
        'annee_academique',
        'fichier_pdf',
        'statut',
        'motif_rejet',
        'telechargement_autorise',
        'embargo_date',
        'etudiant_id',
        'encadreur_id',
        'filiere_id',
        'texte_extrait',
        'score_similarite',
        'memoire_similaire_id',
    ];

    protected $casts = [
        'telechargement_autorise' => 'boolean',
        'embargo_date'            => 'date',
        'score_similarite'        => 'decimal:2',
    ];

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', 'valide');
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function encadreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encadreur_id');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class);
    }

    public function historiqueLectures(): HasMany
    {
        return $this->hasMany(HistoriqueLecture::class);
    }

    public function memoireSimilaire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class, 'memoire_similaire_id');
    }

    public function estVisible(): bool
    {
        return $this->statut === 'valide'
            && ($this->embargo_date === null || $this->embargo_date->isPast());
    }
}
