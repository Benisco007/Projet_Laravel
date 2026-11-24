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
        'titre', 'auteur', 'annee', 'resume',
        'fichier_pdf', 'statut', 'filiere_id', 'user_id',
    ];

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', 'valide');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class);
    }

    public function historiqueLectures(): HasMany
    {
        return $this->hasMany(HistoriqueLecture::class);
    }
}