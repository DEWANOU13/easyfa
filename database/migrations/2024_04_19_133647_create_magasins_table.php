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
        Schema::create('magasins', function (Blueprint $table) {
            $table->id();
            $table->string('Enregistrer_par')->nullable();
            $table->string('Modifier_par')->nullable();
            $table->string('NomMagasin');
            $table->unsignedBigInteger('agence_id');
            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('cascade');
            $table->boolean('Statut_Magasin')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magasins');
    }
};
