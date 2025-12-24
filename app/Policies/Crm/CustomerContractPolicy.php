<?php

namespace App\Policies\Crm;

use App\Models\Crm\Parties\CustomerContract;
use App\Models\User;
use App\Support\Permissions;

class CustomerContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'view'));
    }

    public function view(User $user, CustomerContract $contract): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'create'));
    }

    public function update(User $user, CustomerContract $contract): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'update'));
    }

    public function delete(User $user, CustomerContract $contract): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('customer_contracts', 'export'));
    }

    public function restore(User $user, CustomerContract $contract): bool
    {
        return false;
    }

    public function forceDelete(User $user, CustomerContract $contract): bool
    {
        return false;
    }
}
