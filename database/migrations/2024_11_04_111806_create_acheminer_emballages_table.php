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
        Schema::create('acheminer_emballages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('Id_Acheminement')->nullable();
            $table->bigInteger('Id_Stock_Emballage')->nullable();
            $table->double('Prix_Revient')->nullable();
            $table->double('Qte_acheminee');
            $table->double('Qte_Receptionnee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acheminer_emballages');
    }
};
