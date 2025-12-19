<?php

namespace App\Observers;

use App\Enums\AuditActionType;
use App\Models\Order;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class OrderObserver
{
    use LogsDeletion;

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $order,
            AuditActionType::StatusChange,
            $order->getOriginal(),
            $order->getAttributes()
        );
    }
}
