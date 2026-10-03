<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_challenge_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_challenge_id')->constrained('user_challenges')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('day_number');
            $table->date('log_date');
            $table->boolean('is_completed')->default(true);
            $table->integer('xp_earned')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_challenge_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_challenge_logs');
    }
};
