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
        Schema::create('seuil_stock_produits', function (Blueprint $table) {
            $table->id();
            $table->string('seuil_stock')->nullable();
            $table->unsignedBigInteger('id_produit')->nullable();
            $table->foreign('id_produit')->references('id')->on('produits')->onDelete('SET NULL');
            $table->string('Enregistrer_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seuil_stock_produits');
    }
};
