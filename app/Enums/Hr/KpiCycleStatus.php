<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum KpiCycleStatus: string
{
    use HasStatusTransitions;

    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'warning',
            self::Closed => 'success',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Open => $next === self::Closed,
            self::Closed => false,
        };
    }
}
