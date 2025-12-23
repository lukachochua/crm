<?php

namespace App\Filament\Widgets\Crm;

use App\Models\Crm\Reporting\TurnoverOverview;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Throwable;

class RevenueTrendChart extends ChartWidget
{
    protected static ?int $sort = -25;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Revenue Trend';

    protected static ?string $description = 'Monthly invoiced vs paid totals.';

    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $periods = TurnoverOverview::query()
            ->orderByDesc('period')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $labels = $periods->map(function (TurnoverOverview $overview): string {
            if (! $overview->period) {
                return 'Unknown';
            }

            try {
                return Carbon::createFromFormat('Y-m', $overview->period)->format('M Y');
            } catch (Throwable) {
                return $overview->period;
            }
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Invoiced',
                    'data' => $periods->pluck('total_invoiced')->map(fn ($value): float => (float) $value)->all(),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Paid',
                    'data' => $periods->pluck('total_paid')->map(fn ($value): float => (float) $value)->all(),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
