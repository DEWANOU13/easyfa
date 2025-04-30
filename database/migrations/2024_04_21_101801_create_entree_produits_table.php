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
        Schema::create('entree_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Entree');
            $table->string('Reference_Entree');
            $table->string('Observations');
            $table->unsignedBigInteger('Id_Fournisseur')->nullable();
            $table->foreign('Id_Fournisseur')->references('id')->on('fournisseurs')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence')->nullable();
            $table->foreign('Id_Agence')->references('id')->on('agences')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence_source')->nullable();
            $table->foreign('Id_Agence_source')->references('id')->on('agences')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entree_produits');
    }
};
