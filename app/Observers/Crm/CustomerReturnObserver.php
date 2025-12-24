<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Operations\CustomerReturn;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class CustomerReturnObserver
{
    use LogsDeletion;

    public function updated(CustomerReturn $return): void
    {
        if (! $return->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $return,
            AuditActionType::StatusChange,
            $return->getOriginal(),
            $return->getAttributes()
        );
    }
}
