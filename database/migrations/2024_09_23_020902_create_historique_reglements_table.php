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
        Schema::create('historique_reglements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('Date_Reglement');
            $table->string('Reference_Reglement')->nullable();
            $table->string('Reference_Facture')->nullable();
            $table->double('Montant_Regle');
            $table->string('Operation')->nullable();
            $table->unsignedBigInteger('Id_Client')->nullable();
            $table->foreign('Id_Client')->references('id')->on('clients')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_reglements');
    }
};
