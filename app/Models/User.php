<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'matricule',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function memoires()
    {
        return $this->hasMany(Memoire::class, 'etudiant_id');
    }

    public function memoiresEncadres()
    {
        return $this->hasMany(Memoire::class, 'encadreur_id');
    }

    public function validations()
    {
        return $this->hasMany(\App\Models\Validation::class, 'validateur_id');
    }

    // Helpers de rôle
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isEtudiant(): bool
    {
        return $this->role === 'etudiant';
    }
    public function isValidateur(): bool
    {
        return $this->role === 'validateur';
    }
    public function isEncadreur(): bool
    {
        return $this->role === 'encadreur';
    }
    public function isLecteur(): bool
    {
        return $this->role === 'lecteur';
    }
}
