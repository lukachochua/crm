<?php

namespace App\Filament\Pages\Crm;

use App\Filament\Widgets\Crm\ApplicationStatusChart;
use App\Filament\Widgets\Crm\CrmStatsOverview;
use App\Filament\Widgets\Crm\LatestApplicationsWidget;
use App\Filament\Widgets\Crm\OverdueInvoicesWidget;
use App\Filament\Widgets\Crm\RecentOrdersWidget;
use App\Filament\Widgets\Crm\RecentPaymentsWidget;
use App\Filament\Widgets\Crm\RevenueTrendChart;
use App\Filament\Widgets\Crm\UpcomingReservationsWidget;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

class CrmDashboard extends Dashboard
{
    protected static bool $isDiscovered = false;

    protected static string $routePath = '/crm';

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
