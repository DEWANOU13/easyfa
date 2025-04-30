<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sortie_ligne_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Sortie_Emballage')->nullable();
            $table->bigInteger('Id_Stock_Emballage')->nullable();
            $table->bigInteger('Id_historique_prix_revient')->nullable();
            $table->double('Qte_Sortie');
            $table->string('Enregistrer_par')->nullable();
            $table->string('Modifier_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sortie_ligne_emballages');
    }
};
