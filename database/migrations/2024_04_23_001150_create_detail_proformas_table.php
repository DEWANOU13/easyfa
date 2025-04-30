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
        Schema::create('detail_proformas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
            $table->unsignedBigInteger('GroupeTaxe_id')->nullable();
            $table->foreign('GroupeTaxe_id')->references('id')->on('groupe_taxations')->onDelete('cascade');
            $table->unsignedBigInteger('facture_id')->nullable();
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
            $table->double('Taux_remise')->nullable();
            $table->double('Prix_unitaire_HT')->nullable();
            $table->double('Prix_unitaire_TTC')->nullable();
            $table->double('Qte')->nullable();
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
        Schema::dropIfExists('detail_proformas');
    }
};
