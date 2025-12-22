<?php

namespace App\Filament\Widgets\Hr;

use App\Enums\Hr\RecruitmentStage;
use App\Models\Hr\Recruitment\Candidate;
use Filament\Widgets\ChartWidget;

class RecruitmentPipelineChart extends ChartWidget
{
    protected static ?int $sort = -20;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recruitment Pipeline';

    protected static ?string $description = 'Candidates grouped by stage.';

    protected static ?string $maxHeight = '280px';

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']);
    }

    protected function getData(): array
    {
        $counts = Candidate::query()
            ->selectRaw('stage, COUNT(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $labels = [];
        $data = [];

        foreach (RecruitmentStage::cases() as $stage) {
            $labels[] = $stage->label();
            $data[] = (int) ($counts[$stage->value] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Candidates',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                    'borderColor' => '#059669',
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
