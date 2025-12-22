<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum FeedbackRequestStatus: string
{
    use HasStatusTransitions;

    case Pending = 'pending';
    case Submitted = 'submitted';
    case Cancelled = 'cancelled';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Submitted => 'Submitted',
            self::Cancelled => 'Cancelled',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Submitted => 'info',
            self::Cancelled => 'danger',
            self::Closed => 'success',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Pending => in_array($next, [self::Submitted, self::Cancelled], true),
            self::Submitted => $next === self::Closed,
            self::Cancelled, self::Closed => false,
        };
    }
}
