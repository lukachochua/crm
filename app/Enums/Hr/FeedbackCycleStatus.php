<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum FeedbackCycleStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Open => 'warning',
            self::Closed => 'success',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Open,
            self::Open => $next === self::Closed,
            self::Closed => false,
        };
    }
}
