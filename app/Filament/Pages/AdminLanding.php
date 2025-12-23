<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Crm\CrmDashboard;
use App\Filament\Pages\Hr\HrDashboard;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

class AdminLanding extends Dashboard
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $routePath = '/';

    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user && $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']) && ! $user->hasRole('superadmin')) {
            $this->redirect(HrDashboard::getUrl());
            return;
        }

        $this->redirect(CrmDashboard::getUrl());
    }

    public function getWidgets(): array
    {
        return [];
    }
}
