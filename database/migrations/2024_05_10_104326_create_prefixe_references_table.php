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
        Schema::create('prefixe_references', function (Blueprint $table) {
            $table->id();
            $table->string('entre_produit');
            $table->string('sortie_produit');
            $table->string('transfert_produit');
            $table->string('inventaire');
            $table->string('proforma');
            $table->string('facture');
            $table->string('avoir');
            $table->string('reglement');
            $table->string('libelle_reserves');
            $table->string('approvisionnement');
            $table->string('reception_approvisionnement');
            $table->string('acheminement');
            $table->string('reception_acheminement');
            $table->enum('mode_impression', ['Texte', 'Image'])->default('Texte');
            $table->boolean('prise_en_compte_reglement')->default(0);
            $table->boolean('surplus_reglement')->default(0);
            $table->enum('type_normalisation', ['E-MECEF', 'MCF'])->default('E-MECEF');
            $table->boolean('emballage')->default(0);
            $table->boolean('caisse')->default(0);
            $table->boolean('pre_cocher_aib')->default(0);
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
        Schema::dropIfExists('prefixe_references');
    }
};
