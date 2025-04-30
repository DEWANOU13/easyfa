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
        Schema::create('seuil_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('seuil_stock')->nullable();
            $table->unsignedBigInteger('id_categorie_produit')->nullable();
            $table->foreign('id_categorie_produit')->references('id')->on('categorie_produits')->onDelete('SET NULL');
            $table->string('Enregistrer_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seuil_stocks');
    }
};
