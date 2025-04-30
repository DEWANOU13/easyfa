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
        Schema::create('transfert_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Utilisateur')->nullable();
            $table->dateTime('Date_Transfert');
            $table->string('Reference_Transfert');
            $table->string('Observations');
            $table->bigInteger('Id_Magasin_Source')->nullable();
            $table->bigInteger('Id_Magasin_Destination')->nullable();
            $table->bigInteger('Id_Agence')->nullable();
            $table->bigInteger('Id_Agence_Destination')->nullable();
            $table->timestamps();
            $table->dateTime('Date_Synchro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_emballages');
    }
};
