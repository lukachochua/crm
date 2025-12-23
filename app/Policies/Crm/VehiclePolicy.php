<?php

namespace App\Policies\Crm;

use App\Models\User;
use App\Models\Crm\Assets\Vehicle;
use App\Support\Permissions;

class VehiclePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('vehicles', 'view'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->can(Permissions::permission('vehicles', 'view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('vehicles', 'create'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->can(Permissions::permission('vehicles', 'update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->can(Permissions::permission('vehicles', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('vehicles', 'export'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vehicle $vehicle): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return false;
    }
}
