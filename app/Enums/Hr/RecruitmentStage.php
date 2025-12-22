<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum RecruitmentStage: string
{
    use HasStatusTransitions;

    case Application = 'application';
    case Interview = 'interview';
    case Offer = 'offer';
    case Hired = 'hired';

    public function label(): string
    {
        return match ($this) {
            self::Application => 'Application',
            self::Interview => 'Interview',
            self::Offer => 'Offer',
            self::Hired => 'Hired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Application => 'gray',
            self::Interview => 'warning',
            self::Offer => 'info',
            self::Hired => 'success',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Application => $next === self::Interview,
            self::Interview => $next === self::Offer,
            self::Offer => $next === self::Hired,
            self::Hired => false,
        };
    }
}
