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
        Schema::create('produit_historiques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Produit')->nullable();
            $table->foreign('Id_Produit')->references('id')->on('produits')->onDelete('SET NULL');
            $table->string('Designation');
            $table->string('Enregistrer_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produit_historiques');
    }
};
