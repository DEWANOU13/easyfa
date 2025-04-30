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
        Schema::create('groupe_taxations', function (Blueprint $table) {
            $table->id();
            $table->string('Code_lettre')->unique();
            $table->string('Etiquette')->nullable();
            $table->double('valeur_taxe')->nullable();
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();
            $table->string('Enregistrer_par')->nullable();
            $table->string('Modifier_par')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupe_taxations');
    }
};
