<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Parties\CustomerPricingProfile;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class CustomerPricingProfileObserver
{
    use LogsDeletion;

    public function updated(CustomerPricingProfile $profile): void
    {
        if (! $profile->wasChanged('is_active')) {
            return;
        }

        AuditLogger::record(
            $profile,
            AuditActionType::StatusChange,
            $profile->getOriginal(),
            $profile->getAttributes()
        );
    }
}
