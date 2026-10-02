<?php

namespace App\Filament\Widgets;

use App\Models\HealthTarget;
use Filament\Widgets\ChartWidget;

class HealthTargetDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Target Kesehatan';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $foodCount = HealthTarget::where('type', 'food')->count();
        $drinkCount = HealthTarget::where('type', 'drink')->count();
        $exerciseCount = HealthTarget::where('type', 'exercise')->count();
        $otherCount = HealthTarget::where('type', 'other')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Target',
                    'data' => [$drinkCount, $foodCount, $exerciseCount, $otherCount],
                    'backgroundColor' => [
                        '#0ea5e9', // Sky (Drink)
                        '#f59e0b', // Amber (Food)
                        '#10b981', // Emerald (Exercise)
                        '#8b5cf6', // Violet (Other)
                    ],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => ['Minuman (Drink)', 'Makanan (Food)', 'Olahraga (Exercise)', 'Lainnya (Other)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

