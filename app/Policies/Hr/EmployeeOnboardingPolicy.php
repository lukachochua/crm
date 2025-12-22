<?php

namespace App\Policies\Hr;

use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class EmployeeOnboardingPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'view'));
    }

    public function view(User $user, EmployeeOnboarding $onboarding): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $onboarding->employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'create'));
    }

    public function update(User $user, EmployeeOnboarding $onboarding): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $onboarding->employee);
        }

        return true;
    }

    public function delete(User $user, EmployeeOnboarding $onboarding): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $onboarding->employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'export'));
    }

    public function restore(User $user, EmployeeOnboarding $onboarding): bool
    {
        return false;
    }

    public function forceDelete(User $user, EmployeeOnboarding $onboarding): bool
    {
        return false;
    }
}
