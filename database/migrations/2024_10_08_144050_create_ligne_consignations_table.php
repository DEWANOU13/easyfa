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
        Schema::create('ligne_consignations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emballage_id')->nullable();
            $table->bigInteger('produit_id')->nullable();
            $table->bigInteger('consignation_id')->nullable();
            $table->double('Qte');
            $table->double('restituee')->nullable();
            $table->double('facturee')->nullable();
            $table->bigInteger('stock_emballage_id')->nullable();
            $table->bigInteger('stock_id')->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ligne_consignations');
    }
};
