<?php

namespace App\Policies\Hr;

use App\Models\Hr\ContractType;
use App\Models\User;
use App\Support\Permissions;

class ContractTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('contract_types', 'view'));
    }

    public function view(User $user, ContractType $contractType): bool
    {
        return $user->can(Permissions::permission('contract_types', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('contract_types', 'create'));
    }

    public function update(User $user, ContractType $contractType): bool
    {
        return $user->can(Permissions::permission('contract_types', 'update'));
    }

    public function delete(User $user, ContractType $contractType): bool
    {
        return $user->can(Permissions::permission('contract_types', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('contract_types', 'export'));
    }

    public function restore(User $user, ContractType $contractType): bool
    {
        return false;
    }

    public function forceDelete(User $user, ContractType $contractType): bool
    {
        return false;
    }
}
