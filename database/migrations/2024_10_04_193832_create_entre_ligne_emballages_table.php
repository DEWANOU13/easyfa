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
        Schema::create('entre_ligne_emballages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Entree_Emballage')->nullable();
            $table->foreign('Id_Entree_Emballage')->references('id')->on('entree_emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Emballage')->nullable();
            $table->foreign('Id_Emballage')->references('id')->on('emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Magasin')->nullable();
            $table->foreign('Id_Magasin')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->double('Qte_Entree');
            $table->double('Prix_Achat_Net');
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
        Schema::dropIfExists('entre_ligne_emballages');
    }
};
