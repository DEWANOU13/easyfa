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
        Schema::create('detail_reglements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Reglement')->nullable();
            $table->foreign('Id_Reglement')->references('id')->on('reglements')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Libelle_Type_Operation')->nullable();
            $table->foreign('Id_Libelle_Type_Operation')->references('id')->on('libelle_type_operations')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Facture')->nullable();
            $table->foreign('Id_Facture')->references('id')->on('factures')->onDelete('SET NULL');
            $table->double('Montant_Regle');
            $table->boolean('Statut_Reglement')->default('1');
            $table->boolean('Compensation')->default('0');
            $table->string('Enregistrer_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reglements');
    }
};
