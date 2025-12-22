<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum EmployeeStatus: string
{
    use HasStatusTransitions;

    case Active = 'active';
    case Suspended = 'suspended';
    case Left = 'left';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Left => 'Left',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Left => 'gray',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Active => in_array($next, [self::Suspended, self::Left], true),
            self::Suspended => in_array($next, [self::Active, self::Left], true),
            self::Left => false,
        };
    }
}
