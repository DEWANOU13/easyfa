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
        Schema::create('reception_acheminements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->dateTime('Date_Reception');
            $table->string('Reference_Reception');
            $table->string('Observations');
            $table->bigInteger('Id_Agence')->nullable();
            $table->bigInteger('Id_acheminement')->nullable();
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
        Schema::dropIfExists('reception_acheminements');
    }
};
