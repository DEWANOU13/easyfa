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
        Schema::create('inventorier_embs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Stock_Emballage')->nullable();
            $table->bigInteger('Id_Inventaire_Emballage')->nullable();
            $table->bigInteger('Id_Historique_Prix_Revient')->nullable();
            $table->double('Qte_Initiale');
            $table->double('Qte_Comptee');
            $table->double('Qte_Ecart');
            $table->string('Justificatif')->nullable();
            $table->string('Analyse_Ecart');
            $table->double('Qte_Ajustee');
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
        Schema::dropIfExists('inventorier_embs');
    }
};
