<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum OnboardingStatus: string
{
    use HasStatusTransitions;

    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::NotStarted => in_array($next, [self::InProgress, self::Cancelled], true),
            self::InProgress => in_array($next, [self::Completed, self::Cancelled], true),
            self::Completed, self::Cancelled => false,
        };
    }
}
