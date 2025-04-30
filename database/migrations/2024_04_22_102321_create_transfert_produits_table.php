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
        Schema::create('transfert_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Transfert');
            $table->string('Reference_Transfert');
            $table->string('Observations');
            $table->unsignedBigInteger('Id_Magasin_Source')->nullable();
            $table->foreign('Id_Magasin_Source')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Magasin_Destination')->nullable();
            $table->foreign('Id_Magasin_Destination')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence')->nullable();
            $table->foreign('Id_Agence')->references('id')->on('agences')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence_Destination')->nullable();
            $table->foreign('Id_Agence_Destination')->references('id')->on('agences')->onDelete('SET NULL');
            $table->string('Enregistrer_par')->nullable();
            $table->string('Modifier_par')->nullable();
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_produits');
    }
};
