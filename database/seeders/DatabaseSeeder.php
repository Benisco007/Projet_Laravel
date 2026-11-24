<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            FiliereSeeder::class,
        ]);

        // Garde ici les lignes déjà présentes dans ton fichier (ex. User::factory()),
        // je ne les connais pas.
    }
}