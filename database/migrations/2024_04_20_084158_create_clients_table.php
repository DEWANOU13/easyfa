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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('Denomination_sociale')->unique(true);
            $table->string('Adresse_client')->nullable(true);
            $table->string('Telephone_fixe')->nullable(true);
            $table->string('Telephone_mobile')->nullable(true);
            $table->string('Adresse_mail')->nullable(true);
            $table->string('Pays')->nullable(true);
            $table->string('Numero_ifu', 13)->nullable();
            $table->string('Code_client')->unique(true);
            $table->integer('Categorie_client_id');
            $table->boolean('Statut_client')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('Modifier_par')->nullable(true);
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
