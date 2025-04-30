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
        Schema::create('sortie_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Sortie');
            $table->string('Reference_Sortie');
            $table->string('type_sortie')->nullable();
            $table->string('Observations');
            $table->unsignedBigInteger('Id_Agence')->nullable();
            $table->foreign('Id_Agence')->references('id')->on('agences')->onDelete('SET NULL');
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sortie_produits');
    }
};
