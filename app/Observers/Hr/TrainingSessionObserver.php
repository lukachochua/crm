<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Training\TrainingSession;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class TrainingSessionObserver
{
    use LogsDeletion;

    public function updated(TrainingSession $session): void
    {
        if (! $session->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $session,
            AuditActionType::StatusChange,
            $session->getOriginal(),
            $session->getAttributes()
        );
    }
}
