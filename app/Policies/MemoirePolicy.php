<?php

namespace App\Policies;

use App\Models\Memoire;
use App\Models\User;

class MemoirePolicy
{
    // Seul un étudiant peut déposer
    public function create(User $user): bool
    {
        return $user->isEtudiant();
    }

    // Seul l'étudiant propriétaire peut voir son mémoire en détail
    public function view(User $user, Memoire $memoire): bool
    {
        return $user->isAdmin()
            || $user->isValidateur()
            || $user->id === $memoire->etudiant_id;
    }

    // Seul le validateur ou admin peut changer le statut
    public function statuer(User $user): bool
    {
        return $user->isValidateur() || $user->isAdmin();
    }
    // Un externe ne peut voir que les 4 premières pages (géré côté vue)
    public function viewPreview(User $user, Memoire $memoire): bool
    {
        return $memoire->statut === 'valide'
            && ($memoire->embargo_date === null || $memoire->embargo_date->isPast());
    }
}
