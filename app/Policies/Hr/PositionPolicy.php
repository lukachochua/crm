<?php

namespace App\Policies\Hr;

use App\Models\Hr\Position;
use App\Models\User;
use App\Support\Permissions;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('positions', 'view'));
    }

    public function view(User $user, Position $position): bool
    {
        return $user->can(Permissions::permission('positions', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('positions', 'create'));
    }

    public function update(User $user, Position $position): bool
    {
        return $user->can(Permissions::permission('positions', 'update'));
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->can(Permissions::permission('positions', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('positions', 'export'));
    }

    public function restore(User $user, Position $position): bool
    {
        return false;
    }

    public function forceDelete(User $user, Position $position): bool
    {
        return false;
    }
}
