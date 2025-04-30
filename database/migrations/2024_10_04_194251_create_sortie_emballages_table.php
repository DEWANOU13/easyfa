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
        Schema::create('sortie_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Utilisateur')->nullable();
            $table->dateTime('Date_Sortie');
            $table->string('Reference_Sortie');
            $table->string('type_sortie')->nullable();
            $table->string('Observations');
            $table->bigInteger('Id_Agence')->nullable();
            $table->dateTime('Date_Synchro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sortie_emballages');
    }
};
