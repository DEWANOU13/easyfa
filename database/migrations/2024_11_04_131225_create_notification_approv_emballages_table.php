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
        Schema::create('notification_approv_emballages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Agence_Source')->nullable();
            $table->foreign('Id_Agence_Source')->references('id')->on('agences')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Agence_Destination')->nullable();
            $table->foreign('Id_Agence_Destination')->references('id')->on('agences')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Appro_Emballage')->nullable();
            $table->foreign('Id_Appro_Emballage')->references('id')->on('appro_emballages')->onDelete('SET NULL');
            $table->double('Qte_Approvisionnee');
            $table->boolean('Statut')->default(1);
            $table->string('Motif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_approv_emballages');
    }
};
