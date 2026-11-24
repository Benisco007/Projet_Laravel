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
            $table->text('resume');
            $table->string('mots_cles')->nullable();
            $table->string('annee_academique');
            $table->string('fichier_pdf');
            $table->enum('statut', ['soumis', 'en_verification', 'valide', 'rejete'])->default('soumis');
            $table->text('motif_rejet')->nullable();
            $table->boolean('telechargement_autorise')->default(false);
            $table->date('embargo_date')->nullable();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('encadreur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('filiere_id')->nullable()->constrained('filieres')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memoires');
    }
};
