<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Parties\CustomerContract;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class CustomerContractObserver
{
    use LogsDeletion;

    public function updated(CustomerContract $contract): void
    {
        if (! $contract->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $contract,
            AuditActionType::StatusChange,
            $contract->getOriginal(),
            $contract->getAttributes()
        );
    }
}
