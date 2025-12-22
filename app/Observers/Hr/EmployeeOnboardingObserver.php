<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class EmployeeOnboardingObserver
{
    use LogsDeletion;

    public function updated(EmployeeOnboarding $onboarding): void
    {
        if (! $onboarding->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $onboarding,
            AuditActionType::StatusChange,
            $onboarding->getOriginal(),
            $onboarding->getAttributes()
        );
    }
}
