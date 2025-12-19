<?php

namespace App\Observers;

use App\Enums\AuditActionType;
use App\Models\Reservation;
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
