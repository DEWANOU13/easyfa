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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('actif')->default('0');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('sms_verification_code')->nullable();
            $table->timestamp('sms_verified_at')->nullable();
            $table->boolean('force_password_change')->default('0');
            $table->boolean('identity_verified')->default(false);
            $table->boolean('ejected')->default(0);
            $table->rememberToken();
            $table->string('verification_token')->nullable();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
