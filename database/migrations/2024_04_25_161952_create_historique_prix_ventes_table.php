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
        Schema::create('historique_prix_ventes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Produit')->nullable();
            $table->foreign('Id_Produit')->references('id')->on('produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Categorie_Client')->nullable();
            $table->foreign('Id_Categorie_Client')->references('id')->on('categorie_clients')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence')->nullable();
            $table->foreign('Id_Agence')->references('id')->on('agences')->onDelete('SET NULL');
            $table->dateTime('Date_variation_prix');
            $table->double('Prix_Vente');
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
        Schema::dropIfExists('historique_prix_ventes');
    }
};
