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
        Schema::create('agences', function (Blueprint $table) {
            $table->id();
            $table->string('NomAgence');
            $table->boolean('EnActivite')->default(1);
            $table->string('adresseAgence')->nullable();
            $table->string('numero_telephone_1')->nullable();
            $table->string('numero_telephone_2')->nullable();
            $table->string('titre_signataire_facture')->nullable();
            $table->string('nom_signataire')->nullable();
            $table->string('create_user_id')->nullable();
            $table->string('update_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agences');
    }
};
