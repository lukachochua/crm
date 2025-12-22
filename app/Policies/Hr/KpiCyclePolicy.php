<?php

namespace App\Policies\Hr;

use App\Models\Hr\Kpi\KpiCycle;
use App\Models\User;
use App\Support\Permissions;

class KpiCyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'view'));
    }

    public function view(User $user, KpiCycle $cycle): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'create'));
    }

    public function update(User $user, KpiCycle $cycle): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'update'));
    }

    public function delete(User $user, KpiCycle $cycle): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_cycles', 'export'));
    }

    public function restore(User $user, KpiCycle $cycle): bool
    {
        return false;
    }

    public function forceDelete(User $user, KpiCycle $cycle): bool
    {
        return false;
    }
}
