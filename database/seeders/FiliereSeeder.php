<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            ['code' => 'IG',   'nom' => 'Informatique de Gestion'],
            ['code' => 'GFC',  'nom' => 'Gestion Financière et Comptable'],
            ['code' => 'GRH',  'nom' => 'Gestion des Ressources Humaines'],
            ['code' => 'MCV',  'nom' => 'Marketing, Commerce et Vente'],
            ['code' => 'BFA',  'nom' => 'Banque, Finance et Assurance'],
            ['code' => 'STAT', 'nom' => 'Statistique'],
            ['code' => 'PLAN', 'nom' => 'Planification'],
        ];

        foreach ($filieres as $filiere) {
            Filiere::updateOrCreate(['code' => $filiere['code']], $filiere);
        }
    }
}
