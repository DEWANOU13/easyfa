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
        Schema::create('detail_import_stock_emballages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Import_Stock_Emballage')->nullable();
            $table->foreign('Id_Import_Stock_Emballage')->references('id')->on('import_stock_emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Emballage')->nullable();
            $table->foreign('Id_Emballage')->references('id')->on('emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Magasin')->nullable();
            $table->foreign('Id_Magasin')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->double('Qte_Importee');
            $table->double('Prix_Achat');
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
        Schema::dropIfExists('detail_import_stock_emballages');
    }
};
