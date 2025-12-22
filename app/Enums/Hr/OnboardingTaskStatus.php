<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum OnboardingTaskStatus: string
{
    use HasStatusTransitions;

    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Blocked => 'Blocked',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Blocked => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Pending => in_array($next, [self::InProgress, self::Blocked], true),
            self::InProgress => in_array($next, [self::Completed, self::Blocked], true),
            self::Blocked => $next === self::InProgress,
            self::Completed => false,
        };
    }
}
