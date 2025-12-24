<?php

namespace App\Enums\Crm;

use App\Enums\Concerns\HasStatusTransitions;

enum CustomerReturnStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Reported = 'reported';
    case Reviewed = 'reviewed';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Reported => 'Reported',
            self::Reviewed => 'Reviewed',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Reported => 'info',
            self::Reviewed => 'warning',
            self::Closed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Reported,
            self::Reported => in_array($next, [self::Reviewed, self::Cancelled], true),
            self::Reviewed => $next === self::Closed,
            self::Closed, self::Cancelled => false,
        };
    }
}
