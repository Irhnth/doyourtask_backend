<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\HealthTargetResource;
use App\Filament\Resources\TaskResource;
use App\Filament\Resources\UserResource;
use App\Models\HealthLog;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\Widget;

class WelcomeBannerWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function getGreeting(): string
    {
        $hour = (int) date('H');

        if ($hour >= 4 && $hour < 11) {
            return 'Selamat Pagi';
        }

        if ($hour >= 11 && $hour < 15) {
            return 'Selamat Siang';
        }

        if ($hour >= 15 && $hour < 18) {
            return 'Selamat Sore';
        }

        return 'Selamat Malam';
    }

    public function getPendingTasksCount(): int
    {
        return Task::where('status', 'pending')->count();
    }

    public function getTodayHealthLogsCount(): int
    {
        return HealthLog::whereDate('date', today())->count();
    }

    public function getTotalUsersCount(): int
    {
        return User::count();
    }

    public function getNewTaskUrl(): string
    {
        try {
            return TaskResource::getUrl('create');
        } catch (\Throwable $e) {
            return url('/admin/tasks/create');
        }
    }

    public function getNewHealthTargetUrl(): string
    {
        try {
            return HealthTargetResource::getUrl('create');
        } catch (\Throwable $e) {
            return url('/admin/health-targets/create');
        }
    }

    public function getUsersUrl(): string
    {
        try {
            return UserResource::getUrl('index');
        } catch (\Throwable $e) {
            return url('/admin/users');
        }
    }
}

