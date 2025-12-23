<?php

namespace App\Policies\Crm;

use App\Models\Crm\Billing\Invoice;
use App\Models\User;
use App\Support\Permissions;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('invoices', 'view'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can(Permissions::permission('invoices', 'view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('invoices', 'create'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can(Permissions::permission('invoices', 'update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can(Permissions::permission('invoices', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('invoices', 'export'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return false;
    }
}
