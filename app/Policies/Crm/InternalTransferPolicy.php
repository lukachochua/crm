<?php

namespace App\Policies\Crm;

use App\Models\Crm\Operations\InternalTransfer;
use App\Models\User;
use App\Support\Permissions;

class InternalTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('internal_transfers', 'view'));
    }

    public function view(User $user, InternalTransfer $transfer): bool
    {
        return $user->can(Permissions::permission('internal_transfers', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('internal_transfers', 'create'));
    }

    public function update(User $user, InternalTransfer $transfer): bool
    {
        if ($transfer->isClosedOrCancelled()) {
            return false;
        }

        return $user->can(Permissions::permission('internal_transfers', 'update'));
    }

    public function delete(User $user, InternalTransfer $transfer): bool
    {
        if ($transfer->isClosedOrCancelled()) {
            return false;
        }

        return $user->can(Permissions::permission('internal_transfers', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('internal_transfers', 'export'));
    }

    public function restore(User $user, InternalTransfer $transfer): bool
    {
        return false;
    }

    public function forceDelete(User $user, InternalTransfer $transfer): bool
    {
        return false;
    }
}
