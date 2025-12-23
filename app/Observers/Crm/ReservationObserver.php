<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Models\Crm\Sales\Reservation;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class ReservationObserver
{
    use LogsDeletion;

    public function updated(Reservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $reservation,
            AuditActionType::StatusChange,
            $reservation->getOriginal(),
            $reservation->getAttributes()
        );
    }
}
