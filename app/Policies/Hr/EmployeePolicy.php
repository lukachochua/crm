<?php

namespace App\Policies\Hr;

use App\Models\Hr\Employee;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class EmployeePolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('employees', 'view'));
    }

    public function view(User $user, Employee $employee): bool
    {
        if (! $user->can(Permissions::permission('employees', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('employees', 'create'));
    }

    public function update(User $user, Employee $employee): bool
    {
        if (! $user->can(Permissions::permission('employees', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $employee);
        }

        return true;
    }

    public function delete(User $user, Employee $employee): bool
    {
        if (! $user->can(Permissions::permission('employees', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('employees', 'export'));
    }

    public function restore(User $user, Employee $employee): bool
    {
        return false;
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return false;
    }
}
