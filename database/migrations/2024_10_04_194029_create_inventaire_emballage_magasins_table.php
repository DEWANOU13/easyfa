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
        Schema::create('inventaire_emballage_magasins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Inventaire_Emballage')->nullable();

            $table->bigInteger('Id_Magasin')->nullable();

            $table->bigInteger('Id_Stock_Emballage')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaire_emballage_magasins');
    }
};
