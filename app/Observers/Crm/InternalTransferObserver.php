<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Operations\InternalTransfer;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class InternalTransferObserver
{
    use LogsDeletion;

    public function updated(InternalTransfer $transfer): void
    {
        if (! $transfer->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $transfer,
            AuditActionType::StatusChange,
            $transfer->getOriginal(),
            $transfer->getAttributes()
        );
    }
}
