<?php

namespace App\Enums;

use App\Enums\Concerns\HasStatusTransitions;

enum ReservationStatus: string
{
    use HasStatusTransitions;

    case Active = 'active';
    case Fulfilled = 'fulfilled';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Fulfilled => 'Fulfilled',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'info',
            self::Fulfilled => 'success',
            self::Expired => 'gray',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Active => in_array($next, [self::Fulfilled, self::Expired, self::Cancelled], true),
            self::Fulfilled, self::Expired, self::Cancelled => false,
        };
    }
}
