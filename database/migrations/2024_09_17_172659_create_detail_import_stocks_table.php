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
        Schema::create('detail_import_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Id_Import_Stock')->nullable();
            $table->foreign('Id_Import_Stock')->references('id')->on('import_stocks')->onDelete('SET NULL');
            $table->unsignedBigInteger('Id_Produit')->nullable();
            $table->foreign('Id_Produit')->references('id')->on('produits')->onDelete('SET NULL');
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
        Schema::dropIfExists('detail_import_stocks');
    }
};
