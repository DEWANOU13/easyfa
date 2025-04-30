<?php

use App\Models\User;
use App\Models\Groupe;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groupe_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('groupe_id')->nullable();
            $table->foreign('groupe_id')->references('id')->on('groupes')->onDelete('SET NULL');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupe_users');
    }
};
