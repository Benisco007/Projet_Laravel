<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin ENEAM',     'email' => 'admin@eneam.bj',      'role' => 'admin',      'matricule' => null],
            ['name' => 'Béni Etudiant',   'email' => 'etudiant@eneam.bj',   'role' => 'etudiant',   'matricule' => 'ETU001'],
            ['name' => 'Prof Encadreur',  'email' => 'encadreur@eneam.bj',  'role' => 'encadreur',  'matricule' => null],
            ['name' => 'Mme Validatrice', 'email' => 'validateur@eneam.bj', 'role' => 'validateur', 'matricule' => null],
            ['name' => 'Lecteur Test',    'email' => 'lecteur@eneam.bj',    'role' => 'lecteur',     'matricule' => null],
            ['name' => 'Visiteur Externe', 'email' => 'externe@gmail.com', 'role' => 'externe', 'matricule' => null],
        ];

        foreach ($users as $userData) {
            User::create([
                ...$userData,
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
