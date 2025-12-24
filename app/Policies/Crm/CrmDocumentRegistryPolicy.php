<?php

namespace App\Policies\Crm;

use App\Models\Crm\Reporting\CrmDocumentRegistry;
use App\Models\User;
use App\Support\Permissions;

class CrmDocumentRegistryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasAnyDocumentPermission($user);
    }

    public function view(User $user, CrmDocumentRegistry $registry): bool
    {
        return $this->hasAnyDocumentPermission($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CrmDocumentRegistry $registry): bool
    {
        return false;
    }

    public function delete(User $user, CrmDocumentRegistry $registry): bool
    {
        return false;
    }

    private function hasAnyDocumentPermission(User $user): bool
    {
        return $user->can(Permissions::permission('applications', 'view'))
            || $user->can(Permissions::permission('orders', 'view'))
            || $user->can(Permissions::permission('reservations', 'view'))
            || $user->can(Permissions::permission('invoices', 'view'))
            || $user->can(Permissions::permission('payments', 'view'));
    }
}
