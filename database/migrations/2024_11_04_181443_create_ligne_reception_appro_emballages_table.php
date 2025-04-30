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
        Schema::create('ligne_reception_appro_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Reception_Emballage')->nullable();
            $table->bigInteger('Id_Ligne_Appro_Emballage')->nullable();
            $table->bigInteger('Id_Magasin')->nullable();
            $table->double('Qte_Receptionnee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_reception_appro_emballages');
    }
};
