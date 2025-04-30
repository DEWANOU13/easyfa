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
        Schema::create('operation_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_operation');
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('cascade');
            $table->string('type'); // 'entree', 'sortie', 'ouverture', 'fermeture', etc.
            $table->decimal('montant', 10, 2);
            $table->text('description')->nullable();
            $table->bigInteger('categorie_recette_id')->nullable();
            $table->bigInteger('categorie_depense_id')->nullable();
            $table->string('reference_reglement')->nullable();
            $table->string('statut')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('agence_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_caisses');
    }
};
