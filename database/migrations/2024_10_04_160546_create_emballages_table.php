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
        Schema::create('emballages', function (Blueprint $table) {
            $table->id();
            $table->string('Reference')->unique();
            $table->string('Nom_emballage');
            $table->integer('Categorie_emballage_id')->default(1);
            $table->boolean('Statut_emballage')->default(1);
            $table->string('type_emb')->nullable();
            $table->dateTime('Date_Synchro')->nullable();

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
        Schema::dropIfExists('emballages');
    }
};
