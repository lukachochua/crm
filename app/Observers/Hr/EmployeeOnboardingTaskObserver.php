<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Onboarding\EmployeeOnboardingTask;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class EmployeeOnboardingTaskObserver
{
    use LogsDeletion;

    public function updated(EmployeeOnboardingTask $task): void
    {
        if (! $task->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $task,
            AuditActionType::StatusChange,
            $task->getOriginal(),
            $task->getAttributes()
        );
    }
}
