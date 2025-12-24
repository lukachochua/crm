<?php

namespace App\Policies\Crm;

use App\Models\Crm\Operations\CustomerReturn;
use App\Models\User;
use App\Support\Permissions;

class CustomerReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('customer_returns', 'view'));
    }

    public function view(User $user, CustomerReturn $return): bool
    {
        return $user->can(Permissions::permission('customer_returns', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('customer_returns', 'create'));
    }

    public function update(User $user, CustomerReturn $return): bool
    {
        if ($return->isClosedOrCancelled()) {
            return false;
        }

        return $user->can(Permissions::permission('customer_returns', 'update'));
    }

    public function delete(User $user, CustomerReturn $return): bool
    {
        if ($return->isClosedOrCancelled()) {
            return false;
        }

        return $user->can(Permissions::permission('customer_returns', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('customer_returns', 'export'));
    }

    public function restore(User $user, CustomerReturn $return): bool
    {
        return false;
    }

    public function forceDelete(User $user, CustomerReturn $return): bool
    {
        return false;
    }
}
