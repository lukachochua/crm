<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationStatusChart extends ChartWidget
{
    protected static ?int $sort = -20;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Application Pipeline';

    protected static ?string $description = 'Requests grouped by status.';

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $counts = Application::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $data = [];

        foreach (ApplicationStatus::cases() as $status) {
            $labels[] = $status->label();
            $data[] = (int) ($counts[$status->value] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
