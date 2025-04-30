<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prix_vente_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('categorie_client_id')->nullable();
            $table->foreign('categorie_client_id')->references('id')->on('categorie_clients')->onDelete('SET NULL');
            $table->unsignedBigInteger('agence_id')->nullable();
            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('SET NULL');
            $table->dateTime('date_enregistrement');
            $table->dateTime('date_variation_prix');
            $table->double('prix');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->string('Modifier_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prix_vente_produits');
    }
};
