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
        Schema::create('stock_emballage_histories', function (Blueprint $table) {
            $table->id();
            $table->dateTime('Date');
            $table->unsignedBigInteger('Id_Emballage')->nullable();
            $table->foreign('Id_Emballage')->references('id')->on('emballages')->onDelete('SET NULL');
            $table->unsignedBigInteger('agence_id')->nullable();
            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('cascade');
            $table->unsignedBigInteger('Id_Magasin')->nullable();
            $table->foreign('Id_Magasin')->references('id')->on('magasins')->onDelete('SET NULL');
            $table->string('Motif')->nullable();
            $table->string('Justificatif')->nullable();
            $table->string('operation')->nullable();
            $table->string('type_operation')->nullable();
            $table->double('Quantite');
            $table->double('Prix_vente')->nullable();
            $table->double('Prix_achat')->nullable();
            $table->unsignedBigInteger('Id_Utilisateur')->nullable();
            $table->foreign('Id_Utilisateur')->references('id')->on('users')->onDelete('SET NULL');
            $table->string('Modifier_par')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_emballage_histories');
    }
};
