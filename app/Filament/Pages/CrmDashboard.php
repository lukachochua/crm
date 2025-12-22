<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ApplicationStatusChart;
use App\Filament\Widgets\CrmStatsOverview;
use App\Filament\Widgets\LatestApplicationsWidget;
use App\Filament\Widgets\OverdueInvoicesWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Filament\Widgets\RevenueTrendChart;
use App\Filament\Widgets\UpcomingReservationsWidget;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

class CrmDashboard extends Dashboard
{
    protected static bool $isDiscovered = false;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return ! $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']);
    }

    public function getWidgets(): array
    {
        return [
            CrmStatsOverview::class,
            RevenueTrendChart::class,
            ApplicationStatusChart::class,
            LatestApplicationsWidget::class,
            RecentOrdersWidget::class,
            UpcomingReservationsWidget::class,
            RecentPaymentsWidget::class,
            OverdueInvoicesWidget::class,
        ];
    }
}
