<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes_achat', function (Blueprint $table) {
            $table->unsignedInteger('montant')->default(0);       // en FCFA
            $table->string('reference', 20)->nullable()->unique(); // ex. SIM-AB12CD34
            $table->timestamp('paye_le')->nullable();              // rempli quand le paiement simulé est validé
        });
    }

    public function down(): void
    {
        Schema::table('demandes_achat', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn(['montant', 'reference', 'paye_le']);
        });
    }
};