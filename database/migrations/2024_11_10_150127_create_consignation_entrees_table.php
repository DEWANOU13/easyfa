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
        Schema::create('consignation_entrees', function (Blueprint $table) {
            $table->id();
            $table->string('ref_entree');
            $table->string('entree_id');
            $table->bigInteger('fournisseur_id');
            $table->string('statut')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('agence_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignation_entrees');
    }
};
