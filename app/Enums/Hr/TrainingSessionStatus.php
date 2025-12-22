<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum TrainingSessionStatus: string
{
    use HasStatusTransitions;

    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Scheduled => in_array($next, [self::Completed, self::Cancelled], true),
            self::Completed, self::Cancelled => false,
        };
    }
}
