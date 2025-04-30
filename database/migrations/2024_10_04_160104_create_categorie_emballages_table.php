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
        Schema::create('categorie_emballages', function (Blueprint $table) {
            $table->id();
            $table->string('Libelle');
            $table->boolean('Statut_cat_emballage')->default(1);
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
        Schema::dropIfExists('categorie_emballages');
    }
};
