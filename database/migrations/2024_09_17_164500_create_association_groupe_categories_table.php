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
        Schema::create('association_groupe_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('groupe_categorie_id')->nullable();
            $table->foreign('groupe_categorie_id')->references('id')->on('groupe_categories')->onDelete('cascade');
            $table->unsignedBigInteger('categorie_produit_id')->nullable();
            $table->foreign('categorie_produit_id')->references('id')->on('categorie_produits')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('association_groupe_categories');
    }
};
