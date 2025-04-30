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
        Schema::create('entrer_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Entree_Produit')->nullable();
            $table->foreign('Id_Entree_Produit')->references('id')->on('entree_produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Produit')->nullable();
            $table->foreign('Id_Produit')->references('id')->on('produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Magasin')->nullable();
            $table->foreign('Id_Magasin')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->double('Qte_Entree')->nullable();
            $table->double('Prix_Achat_Net');
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
        Schema::dropIfExists('entrer_produits');
    }
};
