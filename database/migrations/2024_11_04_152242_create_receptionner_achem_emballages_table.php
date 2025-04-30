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
        Schema::create('receptionner_achem_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Reception_Acheminement')->nullable();
            $table->bigInteger('Id_Acheminer')->nullable();
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
        Schema::dropIfExists('receptionner_achem_emballages');
    }
};
