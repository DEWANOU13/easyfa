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
        Schema::create('approvisionners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Approvisionnement')->nullable();
            $table->foreign('Id_Approvisionnement')->references('id')->on('approvisionnements')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Stock')->nullable();
            $table->foreign('Id_Stock')->references('id')->on('stocks')->onDelete('SET NULL');
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
        Schema::dropIfExists('approvisionners');
    }
};
