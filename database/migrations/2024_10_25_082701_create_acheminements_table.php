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
        Schema::create('acheminements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Utilisateur')->nullable();
            $table->dateTime('Date_acheminement');
            $table->string('Reference_acheminement');
            $table->string('Observations');
            $table->string('Statut_acheminement');
            $table->bigInteger('Id_Agence_Source')->nullable();
            $table->bigInteger('Id_Agence_Destination')->nullable();
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
        Schema::dropIfExists('acheminements');
    }
};
