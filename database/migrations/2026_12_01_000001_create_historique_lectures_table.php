<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('memoire_id')->constrained()->cascadeOnDelete();
            $table->date('date_lecture');
            $table->timestamps();

            // Une entrée par jour, par utilisateur et par mémoire
            $table->unique(['user_id', 'memoire_id', 'date_lecture'], 'historique_unique_par_jour');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_lectures');
    }
};
