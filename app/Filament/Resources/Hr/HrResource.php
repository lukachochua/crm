<?php

namespace App\Filament\Resources\Hr;

use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

abstract class HrResource extends Resource
{
    protected static ?string $hrNavigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        if (static::isHrOnlyUser()) {
            return static::$hrNavigationGroup ?? 'HR';
        }

        return 'HR';
    }

    protected static function isHrOnlyUser(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $hrRoles = ['hr_admin', 'hr_manager', 'department_manager'];
        $nonHrRoles = ['superadmin', 'Admin', 'Sales', 'Back Office', 'Finance', 'Turnover'];

        return $user->hasAnyRole($hrRoles) && ! $user->hasAnyRole($nonHrRoles);
    }
}
