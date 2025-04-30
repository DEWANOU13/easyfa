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
        Schema::create('archive_factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_id');
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
            $table->bigInteger('idFacture_originale')->nullable();
            $table->string('Reference_facture')->unique();
            $table->dateTime('Date_facture')->nullable();
            $table->dateTime('Modifie_le')->nullable();
            $table->bigInteger('Compteur_type_facture')->nullable();
            $table->bigInteger('Compteur_total')->nullable();
            $table->string('Code_type_facture', 2)->nullable();
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
            $table->string('Nom_user')->nullable();
            $table->bigInteger('modifier_par')->nullable();
            $table->string('Nom_modifier')->nullable();
            $table->string('Code_client')->nullable();
            $table->string('Nom_client')->nullable();
            $table->string('Telephone_client')->nullable();
            $table->string('Ifu_client')->nullable();
            $table->double('Net_a_payer')->nullable();
            $table->text('Autres_infos')->nullable();
            $table->string('Numero_ifu_machine', 13)->nullable();
            $table->bigInteger('agence_id')->nullable();
            $table->string('Nom_agence')->nullable();
            $table->string('Adresse_agence')->nullable();
            $table->string('Telephone_agence')->nullable();
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_factures');
    }
};
