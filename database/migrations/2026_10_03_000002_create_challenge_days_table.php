<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->integer('day_number'); // 1 sampai 28
            $table->integer('week_number'); // 1 sampai 4
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('mission_type')->default('custom'); // 'task', 'health', 'focus', 'custom'
            $table->integer('target_metric')->default(1);
            $table->integer('reward_xp')->default(25);
            $table->integer('milestone_bonus_xp')->default(0); // Bonus di akhir pekan (Hari 7, 14, 21, 28)
            $table->timestamps();

            $table->unique(['challenge_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_days');
    }
};
