<?php

namespace App\Enums;

use App\Enums\Concerns\HasStatusTransitions;

enum ApplicationStatus: string
{
    use HasStatusTransitions;

    case NewRequest = 'new';
    case Reviewed = 'reviewed';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Converted = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::NewRequest => 'New',
            self::Reviewed => 'Reviewed',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Converted => 'Converted',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NewRequest => 'gray',
            self::Reviewed => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Converted => 'info',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::NewRequest => $next === self::Reviewed,
            self::Reviewed => in_array($next, [self::Approved, self::Rejected], true),
            self::Approved => $next === self::Converted,
            self::Rejected, self::Converted => false,
        };
    }
}
