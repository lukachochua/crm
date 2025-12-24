<?php

namespace App\Policies\Crm;

use App\Models\Crm\Parties\CustomerPricingProfile;
use App\Models\User;
use App\Support\Permissions;

class CustomerPricingProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'view'));
    }

    public function view(User $user, CustomerPricingProfile $profile): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'create'));
    }

    public function update(User $user, CustomerPricingProfile $profile): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'update'));
    }

    public function delete(User $user, CustomerPricingProfile $profile): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('customer_pricing_profiles', 'export'));
    }

    public function restore(User $user, CustomerPricingProfile $profile): bool
    {
        return false;
    }

    public function forceDelete(User $user, CustomerPricingProfile $profile): bool
    {
        return false;
    }
}
