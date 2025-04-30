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
        Schema::create('archive_ligne_factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_factures_id')->nullable();
            $table->foreign('archive_factures_id')->references('id')->on('archive_factures')->onDelete('cascade');
            $table->unsignedBigInteger('stocks_id')->nullable();
            $table->foreign('stocks_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->string('Produit_designation')->nullable();
            $table->unsignedBigInteger('GroupeTaxe_id')->nullable();
            $table->foreign('GroupeTaxe_id')->references('id')->on('groupe_taxations')->onDelete('cascade');
            $table->double('Taux_remise')->nullable();
            $table->double('Prix_unitaire_HT')->nullable();
            $table->double('Prix_revient')->nullable();
            $table->double('Qte')->nullable();
            $table->boolean('is_emballage')->default('0');
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
        Schema::dropIfExists('archive_ligne_factures');
    }
};
