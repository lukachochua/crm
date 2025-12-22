<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Recruitment\Candidate;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class CandidateObserver
{
    use LogsDeletion;

    public function updated(Candidate $candidate): void
    {
        if (! $candidate->wasChanged('stage')) {
            return;
        }

        AuditLogger::record(
            $candidate,
            AuditActionType::StatusChange,
            $candidate->getOriginal(),
            $candidate->getAttributes()
        );
    }
}
