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
        Schema::create('total_factures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('facture_id');
            $table->boolean('Statut');
            $table->double('TotalExoneree');
            $table->double('TotalHT_B');
            $table->double('TotalTVA_B');
            $table->double('TotalHT_C');
            $table->double('TotalHT_D');
            $table->double('TotalTVA_D');
            $table->double('TotalHT_E');
            $table->double('TotalHT_F');
            $table->double('Aib_facturee');
            $table->double('Aib_deductible');
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
        Schema::dropIfExists('total_factures');
    }
};
