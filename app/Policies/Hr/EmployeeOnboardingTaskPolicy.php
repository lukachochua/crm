<?php

namespace App\Policies\Hr;

use App\Models\Hr\Onboarding\EmployeeOnboardingTask;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class EmployeeOnboardingTaskPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'view'));
    }

    public function view(User $user, EmployeeOnboardingTask $task): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $task->loadMissing('onboarding.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $task->onboarding ? $this->scopedEmployee($user, $task->onboarding->employee) : false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'create'));
    }

    public function update(User $user, EmployeeOnboardingTask $task): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $task->loadMissing('onboarding.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $task->onboarding ? $this->scopedEmployee($user, $task->onboarding->employee) : false;
        }

        return true;
    }

    public function delete(User $user, EmployeeOnboardingTask $task): bool
    {
        if (! $user->can(Permissions::permission('employee_onboardings', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $task->loadMissing('onboarding.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $task->onboarding ? $this->scopedEmployee($user, $task->onboarding->employee) : false;
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('employee_onboardings', 'export'));
    }

    public function restore(User $user, EmployeeOnboardingTask $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, EmployeeOnboardingTask $task): bool
    {
        return false;
    }
}
