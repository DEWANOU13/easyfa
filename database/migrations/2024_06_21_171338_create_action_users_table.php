<?php

use App\Models\User;
use App\Models\Action;
use App\Models\Agence;
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
        Schema::create('action_users', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Action::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Agence::class)->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'action_id', 'agence_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_users');
    }
};
