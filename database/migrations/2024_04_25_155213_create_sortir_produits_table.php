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
        Schema::create('sortir_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Sortie_Produit')->nullable();
            $table->foreign('Id_Sortie_Produit')->references('id')->on('sortie_produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Stock')->nullable();
            $table->foreign('Id_Stock')->references('id')->on('stocks')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_historique_prix_revient')->nullable();
            $table->foreign('Id_historique_prix_revient')->references('id')->on('historique_prix_revients')->onDelete('SET NULL');
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
        Schema::dropIfExists('sortir_produits');
    }
};
