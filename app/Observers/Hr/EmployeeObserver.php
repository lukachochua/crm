<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Employee;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class EmployeeObserver
{
    use LogsDeletion;

    public function updated(Employee $employee): void
    {
        if (! $employee->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $employee,
            AuditActionType::StatusChange,
            $employee->getOriginal(),
            $employee->getAttributes()
        );
    }
}
