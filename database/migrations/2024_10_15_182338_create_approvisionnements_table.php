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
        Schema::create('approvisionnements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Appro');
            $table->string('Reference_Approvisionnement');
            $table->string('Observations');
            $table->string('Statut_appro');
            $table->unsignedBigInteger('Id_Agence_Source')->nullable();
            $table->foreign('Id_Agence_Source')->references('id')->on('agences')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence_Destination')->nullable();
            $table->foreign('Id_Agence_Destination')->references('id')->on('agences')->onDelete('SET NULL');
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
        Schema::dropIfExists('approvisionnements');
    }
};
