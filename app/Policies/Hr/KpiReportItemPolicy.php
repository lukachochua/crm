<?php

namespace App\Policies\Hr;

use App\Models\Hr\Kpi\KpiReportItem;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class KpiReportItemPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'view'));
    }

    public function view(User $user, KpiReportItem $item): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $item->loadMissing('report.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $item->report ? $this->scopedEmployee($user, $item->report->employee) : false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'create'));
    }

    public function update(User $user, KpiReportItem $item): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $item->loadMissing('report.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $item->report ? $this->scopedEmployee($user, $item->report->employee) : false;
        }

        return true;
    }

    public function delete(User $user, KpiReportItem $item): bool
    {
        if (! $user->can(Permissions::permission('kpi_reports', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $item->loadMissing('report.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $item->report ? $this->scopedEmployee($user, $item->report->employee) : false;
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('kpi_reports', 'export'));
    }

    public function restore(User $user, KpiReportItem $item): bool
    {
        return false;
    }

    public function forceDelete(User $user, KpiReportItem $item): bool
    {
        return false;
    }
}
