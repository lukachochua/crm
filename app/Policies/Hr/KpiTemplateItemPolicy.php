<?php

namespace App\Policies\Hr;

use App\Models\Hr\Kpi\KpiTemplateItem;
use App\Models\User;
use App\Support\Permissions;

class KpiTemplateItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'view'));
    }

    public function view(User $user, KpiTemplateItem $item): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'create'));
    }

    public function update(User $user, KpiTemplateItem $item): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'update'));
    }

    public function delete(User $user, KpiTemplateItem $item): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_templates', 'export'));
    }

    public function restore(User $user, KpiTemplateItem $item): bool
    {
        return false;
    }

    public function forceDelete(User $user, KpiTemplateItem $item): bool
    {
        return false;
    }
}
