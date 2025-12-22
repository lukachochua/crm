<?php

namespace App\Filament\Pages\Hr;

use App\Filament\Widgets\Hr\HrStatsOverview;
use App\Filament\Widgets\Hr\OverdueOnboardingWidget;
use App\Filament\Widgets\Hr\RecruitmentPipelineChart;
use App\Filament\Widgets\Hr\UpcomingTrainingSessionsWidget;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

class HrDashboard extends Dashboard
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $routePath = '/hr';

    protected static ?string $title = 'HR Dashboard';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']);
    }

    public function getWidgets(): array
    {
        return [
            HrStatsOverview::class,
            RecruitmentPipelineChart::class,
            UpcomingTrainingSessionsWidget::class,
            OverdueOnboardingWidget::class,
        ];
    }
}
