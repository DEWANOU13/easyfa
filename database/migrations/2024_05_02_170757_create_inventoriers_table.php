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
        Schema::create('inventoriers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Stock')->nullable();
            $table->foreign('Id_Stock')->references('id')->on('stocks')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Inventaire_Produit')->nullable();
            $table->foreign('Id_Inventaire_Produit')->references('id')->on('inventaire_produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Historique_Prix_Revient')->nullable();
            $table->foreign('Id_Historique_Prix_Revient')->references('id')->on('historique_prix_revients')->onDelete('SET NULL');
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
        Schema::dropIfExists('inventoriers');
    }
};
