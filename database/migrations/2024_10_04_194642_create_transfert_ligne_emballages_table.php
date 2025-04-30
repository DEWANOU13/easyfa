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
        Schema::create('transfert_ligne_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Emballage')->nullable();
            $table->bigInteger('Id_Transfert_Emballage')->nullable();
            $table->double('Qte_transferee');
            $table->string('Enregistrer_par')->nullable();
            $table->string('Modifier_par')->nullable();
            $table->dateTime('Date_Synchro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_ligne_emballages');
    }
};
