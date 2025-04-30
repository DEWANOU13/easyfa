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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->double('fonds_initial');
            $table->double('fonds_actuel');
            $table->dateTime('date_ouverture')->nullable();
            $table->dateTime('date_fermeture')->nullable();
            $table->boolean('statut')->default(1);
            $table->bigInteger('agence_id');
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};
