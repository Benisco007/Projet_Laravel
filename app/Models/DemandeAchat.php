<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeAchat extends Model
{
    use HasFactory;

    protected $table = 'demandes_achat';

    // Clé enregistrée en base => texte affiché à l'utilisateur
    public const MODES = [
        'en_ligne' => 'Version numérique complète (PDF)',
        'physique' => 'Exemplaire imprimé',
        'bibliotheque' => 'Consultation à la bibliothèque',
    ];

    // Prix en FCFA (exemples pour la simulation : modifie-les ici, et nulle part ailleurs)
    public const PRIX = [
        'en_ligne' => 2000,
        'physique' => 5000,
        'bibliotheque' => 0,
    ];

    // Moyens de paiement simulés
    public const MOYENS = [
        'mtn' => 'MTN Mobile Money',
        'moov' => 'Moov Money',
    ];

    protected $fillable = [
        'user_id', 'memoire_id', 'mode', 'statut', 'montant', 'reference', 'paye_le',
    ];

    protected $casts = [
        'paye_le' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function memoire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class);
    }

    public function libelleMode(): string
    {
        return self::MODES[$this->mode] ?? $this->mode;
    }

    public static function prixLibelle(int $montant): string
    {
        return $montant > 0 ? number_format($montant, 0, ',', ' ') . ' FCFA' : 'Gratuit';
    }
}