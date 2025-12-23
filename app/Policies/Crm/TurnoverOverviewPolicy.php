<?php

namespace App\Policies\Crm;

use App\Models\Crm\Reporting\TurnoverOverview;
use App\Models\User;
use App\Support\Permissions;

class TurnoverOverviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('turnover', 'view'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TurnoverOverview $turnoverOverview): bool
    {
        return $user->can(Permissions::permission('turnover', 'view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TurnoverOverview $turnoverOverview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TurnoverOverview $turnoverOverview): bool
    {
        return false;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('turnover', 'export'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TurnoverOverview $turnoverOverview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TurnoverOverview $turnoverOverview): bool
    {
        return false;
    }
}
