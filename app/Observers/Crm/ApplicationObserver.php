<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Application;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class ApplicationObserver
{
    use LogsDeletion;

    public function updated(Application $application): void
    {
        if (! $application->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $application,
            AuditActionType::StatusChange,
            $application->getOriginal(),
            $application->getAttributes()
        );
    }
}
