<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'telechargement_autorise' => 'boolean',
        'embargo_date'            => 'date',
    ];

    // Relations
    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function encadreur()
    {
        return $this->belongsTo(User::class, 'encadreur_id');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function validations()
    {
        return $this->hasMany(Validation::class);
    }

    // Helper : est-ce que le mémoire est lisible publiquement ?
    public function estVisible(): bool
    {
        return $this->statut === 'valide'
            && ($this->embargo_date === null || $this->embargo_date->isPast());
    }
}
