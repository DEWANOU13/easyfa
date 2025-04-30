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
        Schema::create('ligne_appro_emballages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Appro_Emballage')->nullable();
            $table->foreign('Id_Appro_Emballage')->references('id')->on('appro_emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Stock_Emballage')->nullable();
            $table->foreign('Id_Stock_Emballage')->references('id')->on('stock_emballages')->onDelete('SET NULL');
            $table->double('Prix_Revient');
            $table->double('Qte_Approvisionnee');
            $table->double('Qte_Receptionnee')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_appro_emballages');
    }
};
