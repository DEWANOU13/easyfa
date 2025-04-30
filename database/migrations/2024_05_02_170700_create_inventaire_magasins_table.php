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
        Schema::create('inventaire_magasins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Inventaire_Produit')->nullable();
            $table->foreign('Id_Inventaire_Produit')->references('id')->on('inventaire_produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Magasin')->nullable();
            $table->foreign('Id_Magasin')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Stock')->nullable();
            $table->foreign('Id_Stock')->references('id')->on('stocks')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaire_magasins');
    }
};
