<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum SurveyStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Open => 'Open',
            self::Closed => 'Closed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Open => 'warning',
            self::Closed => 'success',
            self::Archived => 'secondary',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Open,
            self::Open => $next === self::Closed,
            self::Closed => $next === self::Archived,
            self::Archived => false,
        };
    }
}
