<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memoires', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('auteur');
            $table->unsignedSmallInteger('annee');
            $table->text('resume')->nullable();
            $table->string('fichier_pdf');
            $table->string('statut', 20)->default('en_attente'); // en_attente | valide | rejete
            $table->decimal('taux_similarite', 5, 2)->nullable(); // rempli par la détection de plagiat
            $table->foreignId('filiere_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memoires');
    }
};