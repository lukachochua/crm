<?php

namespace App\Policies\Hr;

use App\Models\Hr\Department;
use App\Models\User;
use App\Support\Permissions;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('departments', 'view'));
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can(Permissions::permission('departments', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('departments', 'create'));
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can(Permissions::permission('departments', 'update'));
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->can(Permissions::permission('departments', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('departments', 'export'));
    }

    public function restore(User $user, Department $department): bool
    {
        return false;
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return false;
    }
}
