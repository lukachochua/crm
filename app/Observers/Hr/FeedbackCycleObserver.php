<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Feedback\FeedbackCycle;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class FeedbackCycleObserver
{
    use LogsDeletion;

    public function updated(FeedbackCycle $cycle): void
    {
        if (! $cycle->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $cycle,
            AuditActionType::StatusChange,
            $cycle->getOriginal(),
            $cycle->getAttributes()
        );
    }
}
