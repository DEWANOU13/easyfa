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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('Type');
            $table->string('Reference');
            $table->string('Designation');
            $table->unsignedBigInteger('Id_Categorie')->nullable();
            $table->foreign('Id_Categorie')->references('id')->on('categorie_produits')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Unite_Comptage')->nullable();
            $table->foreign('Id_Unite_Comptage')->references('id')->on('unite_comptages')->onDelete('SET NULL');
            $table->string('Emballage_id')->nullable();
            $table->string('type_emballage')->nullable();
            $table->string('Statut');
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
        Schema::dropIfExists('produits');
    }
};
