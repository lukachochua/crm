<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class FeedbackRequestObserver
{
    use LogsDeletion;

    public function updated(FeedbackRequest $request): void
    {
        if (! $request->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $request,
            AuditActionType::StatusChange,
            $request->getOriginal(),
            $request->getAttributes()
        );
    }
}
