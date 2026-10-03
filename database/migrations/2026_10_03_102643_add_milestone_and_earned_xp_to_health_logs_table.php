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
        Schema::table('health_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('health_logs', 'last_milestone')) {
                $table->integer('last_milestone')->default(0)->after('is_completed');
            }
            if (!Schema::hasColumn('health_logs', 'earned_xp')) {
                $table->integer('earned_xp')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_logs', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('health_logs', 'last_milestone')) {
                $dropCols[] = 'last_milestone';
            }
            if (Schema::hasColumn('health_logs', 'earned_xp')) {
                $dropCols[] = 'earned_xp';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};

