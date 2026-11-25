<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_achat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('memoire_id')->constrained()->cascadeOnDelete();
            $table->string('mode', 20);                       // en_ligne | physique | bibliotheque
            $table->string('statut', 20)->default('nouvelle'); // nouvelle | traitee
            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_achat');
    }
};