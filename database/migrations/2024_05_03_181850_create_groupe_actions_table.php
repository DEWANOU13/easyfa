<?php

use App\Models\Action;
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
        Schema::create('groupe_actions', function (Blueprint $table) {
            $table->foreignIdFor(Groupe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Action::class)->constrained()->cascadeOnDelete();
            $table->primary(['groupe_id', 'action_id']);
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
        Schema::dropIfExists('groupe_actions');
    }
};
