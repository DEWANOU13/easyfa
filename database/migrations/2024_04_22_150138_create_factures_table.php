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
        Schema::create('factures', function (Blueprint $table) {


        $table->id();
        $table->bigInteger('idFacture_originale')->nullable();
        $table->string('Reference_facture')->unique();
        $table->dateTime('Date_facture')->nullable();
        $table->dateTime('Modifie_le')->nullable();
        $table->bigInteger('Compteur_type_facture')->nullable();
        $table->bigInteger('Compteur_total')->nullable();
        $table->string('Code_type_facture',2)->nullable();
        $table->dateTime('Date_signature')->nullable();
        $table->string('Nim_machine')->nullable();
        $table->string('Code_signature')->nullable();
        $table->text('QrCode')->nullable();
        $table->string('Statut_facture')->nullable();
        $table->boolean('Aib')->nullable();
        $table->integer('Aib_deductible')->nullable();
        $table->string('Commentaire')->nullable();
        $table->string('Objet_facture')->nullable();
        $table->string('Validite')->nullable();
        $table->bigInteger('user_id')->nullable();
        $table->bigInteger('modifier_par')->nullable();

        $table->unsignedBigInteger('client_id')->nullable();
        $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

        $table->double('Net_a_payer')->nullable();
        $table->text('Autres_infos')->nullable();
        $table->string('Numero_ifu_machine',13)->nullable();

        $table->unsignedBigInteger('agence_id')->nullable();
        $table->foreign('agence_id')->references('id')->on('agences')->onDelete('cascade');

        $table->timestamps();
        $table->dateTime('Date_Synchro')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
