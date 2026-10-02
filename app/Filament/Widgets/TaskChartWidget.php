<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TaskChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Aktivitas Tugas (7 Hari Terakhir)';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '280px';

    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            return Carbon::today()->subDays($daysAgo);
        });

        $labels = $days->map(fn ($date) => $date->translatedFormat('d M'))->toArray();

        $tasksCreated = $days->map(function ($date) {
            return Task::whereDate('created_at', $date)->count();
        })->toArray();

        $tasksCompleted = $days->map(function ($date) {
            return Task::where('status', 'completed')
                ->whereDate('updated_at', $date)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tugas Dibuat',
                    'data' => $tasksCreated,
                    'borderColor' => '#f59e0b', // Amber
                    'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Tugas Selesai',
                    'data' => $tasksCompleted,
                    'borderColor' => '#10b981', // Emerald
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

