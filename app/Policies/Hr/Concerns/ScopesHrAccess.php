<?php

namespace App\Policies\Hr\Concerns;

use App\Models\Hr\Employee;
use App\Models\User;

trait ScopesHrAccess
{
    protected function isSuperadmin(User $user): bool
    {
        return $user->hasRole('superadmin');
    }

    protected function isHrAdmin(User $user): bool
    {
        return $user->hasRole('hr_admin');
    }

    protected function isHrManager(User $user): bool
    {
        return $user->hasRole('hr_manager');
    }

    protected function isDepartmentManager(User $user): bool
    {
        return $user->hasRole('department_manager');
    }

    protected function canBypassScope(User $user): bool
    {
        return $this->isSuperadmin($user) || $this->isHrAdmin($user);
    }

    protected function managerEmployee(User $user): ?Employee
    {
        return Employee::query()->where('user_id', $user->id)->first();
    }

    protected function scopedEmployee(User $user, ?Employee $employee): bool
    {
        if (! $employee) {
            return false;
        }

        $managerEmployee = $this->managerEmployee($user);
        if (! $managerEmployee) {
            return false;
        }

        if ($employee->manager_user_id === $user->id) {
            return true;
        }

        return $employee->department_id === $managerEmployee->department_id;
    }

    protected function scopedEmployeeById(User $user, ?int $employeeId): bool
    {
        if (! $employeeId) {
            return false;
        }

        $employee = Employee::query()->find($employeeId);

        return $employee ? $this->scopedEmployee($user, $employee) : false;
    }

    protected function scopedEmployeeByUserId(User $user, ?int $subjectUserId): bool
    {
        if (! $subjectUserId) {
            return false;
        }

        $employee = Employee::query()->where('user_id', $subjectUserId)->first();

        return $employee ? $this->scopedEmployee($user, $employee) : false;
    }
}
