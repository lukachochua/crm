<?php

namespace App\Enums\Crm;

use App\Enums\Concerns\HasStatusTransitions;

enum ContractStatus: string
{
    use HasStatusTransitions;

    case Active = 'active';
    case Expired = 'expired';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Expired => 'Expired',
            self::Terminated => 'Terminated',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Expired => 'warning',
            self::Terminated => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Active => in_array($next, [self::Expired, self::Terminated], true),
            self::Expired => $next === self::Terminated,
            self::Terminated => false,
        };
    }
}
