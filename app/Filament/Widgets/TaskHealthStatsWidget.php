<?php

namespace App\Filament\Widgets;

use App\Models\Badge;
use App\Models\HealthLog;
use App\Models\Level;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskHealthStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    // Refresh data otomatis setiap 30 detik
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // 1. Data Pengguna
        $totalUsers = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();

        // 2. Data Tugas
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // 3. Data Gamifikasi (XP, Level, Badge)
        $totalXp = User::sum('current_xp');
        $totalLevels = Level::count();
        $totalBadges = Badge::count();

        // 4. Data Log Kesehatan Hari Ini
        $todayLogs = HealthLog::whereDate('date', today())->get();
        $todayLogsCount = $todayLogs->count();
        $todayCompletedLogs = $todayLogs->where('is_completed', true)->count();
        $healthRate = $todayLogsCount > 0 ? round(($todayCompletedLogs / $todayLogsCount) * 100) : 0;

        // Sparkline 7 hari terakhir
        $recentDays = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->toDateString());
        $userTrend = $recentDays->map(fn ($date) => User::whereDate('created_at', $date)->count())->toArray();
        $taskTrend = $recentDays->map(fn ($date) => Task::whereDate('created_at', $date)->count())->toArray();

        return [
            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description("+{$newUsersThisWeek} pengguna baru minggu ini")
                ->descriptionIcon('heroicon-m-user-plus')
                ->chart($userTrend)
                ->color('info'),

            Stat::make('Tingkat Penyelesaian Tugas', "{$taskCompletionRate}%")
                ->description("{$completedTasks} selesai • {$pendingTasks} pending dari {$totalTasks} tugas")
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($taskTrend)
                ->color($taskCompletionRate >= 70 ? 'success' : ($taskCompletionRate >= 40 ? 'warning' : 'danger')),

            Stat::make('Total Akumulasi XP', number_format($totalXp) . ' XP')
                ->description("{$totalLevels} level aktif • {$totalBadges} lencana penghargaan")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            Stat::make('Log Kesehatan Hari Ini', "{$todayCompletedLogs} / {$todayLogsCount}")
                ->description($todayLogsCount > 0 ? "{$healthRate}% target harian terpenuhi" : "Belum ada log hari ini")
                ->descriptionIcon('heroicon-m-heart')
                ->color($healthRate >= 60 ? 'success' : 'danger'),
        ];
    }
}

