<?php

namespace App\Enums\Crm;

use App\Enums\Concerns\HasStatusTransitions;

enum InternalTransferStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Submitted = 'submitted';
    case Acknowledged = 'acknowledged';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Acknowledged => 'Acknowledged',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted => 'info',
            self::Acknowledged => 'warning',
            self::Closed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Submitted,
            self::Submitted => in_array($next, [self::Acknowledged, self::Cancelled], true),
            self::Acknowledged => $next === self::Closed,
            self::Closed, self::Cancelled => false,
        };
    }
}
