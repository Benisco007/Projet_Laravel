<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Colonnes utilisées par la détection de plagiat.
     * Nouvelle migration (on ne modifie jamais celle de Béni).
     */
    public function up(): void
    {
        Schema::table('memoires', function (Blueprint $table) {
            $table->longText('texte_extrait')->nullable();
            $table->decimal('score_similarite', 5, 2)->nullable();
            $table->foreignId('memoire_similaire_id')
                ->nullable()
                ->constrained('memoires')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('memoires', function (Blueprint $table) {
            $table->dropConstrainedForeignId('memoire_similaire_id');
            $table->dropColumn(['texte_extrait', 'score_similarite']);
        });
    }
};
