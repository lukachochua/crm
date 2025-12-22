<?php

namespace App\Policies\Hr;

use App\Models\Hr\Branch;
use App\Models\User;
use App\Support\Permissions;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('branches', 'view'));
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->can(Permissions::permission('branches', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('branches', 'create'));
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->can(Permissions::permission('branches', 'update'));
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->can(Permissions::permission('branches', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('branches', 'export'));
    }

    public function restore(User $user, Branch $branch): bool
    {
        return false;
    }

    public function forceDelete(User $user, Branch $branch): bool
    {
        return false;
    }
}
