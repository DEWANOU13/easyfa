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
        Schema::create('inventaire_emballages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Inventaire');
            $table->string('Reference_Inventaire');
            $table->enum('Statut_Inventaire', ['EN COURS', 'BOUCLER'])->default('EN COURS');
            $table->string('Observations');
            $table->unsignedBigInteger('Id_Agence')->nullable();
            $table->foreign('Id_Agence')->references('id')->on('agences')->onDelete('SET NULL');
            $table->dateTime('Date_Synchro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaire_emballages');
    }
};
