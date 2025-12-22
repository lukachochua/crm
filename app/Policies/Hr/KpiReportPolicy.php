<?php

namespace App\Policies\Hr;

use App\Models\Hr\Kpi\KpiReport;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class KpiReportPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'view'));
    }

    public function view(User $user, KpiReport $report): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $report->employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'create'));
    }

    public function update(User $user, KpiReport $report): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $report->employee);
        }

        return true;
    }

    public function delete(User $user, KpiReport $report): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $report->employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'export'));
    }

    public function restore(User $user, KpiReport $report): bool
    {
        return false;
    }

    public function forceDelete(User $user, KpiReport $report): bool
    {
        return false;
    }
}
