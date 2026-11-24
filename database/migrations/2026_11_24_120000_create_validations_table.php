<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique des décisions prises sur un mémoire.
     * Une ligne = un changement de statut effectué par un validateur.
     */
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memoire_id')->constrained('memoires')->cascadeOnDelete();
            // Vide quand la décision est prise automatiquement (détection de plagiat).
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('statut_precedent', ['soumis', 'en_verification', 'valide', 'rejete']);
            $table->enum('statut_nouveau', ['soumis', 'en_verification', 'valide', 'rejete']);
            $table->text('motif_rejet')->nullable();
            $table->timestamps();

            $table->index(['memoire_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
