<?php

namespace App\Enums\Crm;

use App\Enums\Concerns\HasStatusTransitions;

enum PaymentStatus: string
{
    use HasStatusTransitions;

    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Reversed = 'reversed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Reversed => 'Reversed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Reversed => 'gray',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Pending => in_array($next, [self::Completed, self::Failed], true),
            self::Completed => $next === self::Reversed,
            self::Failed, self::Reversed => false,
        };
    }
}
