<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('target_end_date')->nullable();
            $table->integer('current_day')->default(1);
            $table->enum('status', ['active', 'completed', 'failed', 'abandoned'])->default('active');
            $table->integer('current_streak')->default(0);
            $table->integer('total_xp_earned')->default(0);
            $table->date('last_completed_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
    }
};
