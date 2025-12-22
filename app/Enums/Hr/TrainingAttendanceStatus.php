<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum TrainingAttendanceStatus: string
{
    use HasStatusTransitions;

    case Invited = 'invited';
    case Confirmed = 'confirmed';
    case Attended = 'attended';
    case NoShow = 'no_show';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invited',
            self::Confirmed => 'Confirmed',
            self::Attended => 'Attended',
            self::NoShow => 'No Show',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Invited => 'gray',
            self::Confirmed => 'info',
            self::Attended => 'success',
            self::NoShow => 'warning',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Invited => $next === self::Confirmed,
            self::Confirmed => in_array($next, [self::Attended, self::NoShow, self::Cancelled], true),
            self::Attended, self::NoShow, self::Cancelled => false,
        };
    }
}
