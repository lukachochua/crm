<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum KpiReportStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Submitted = 'submitted';
    case ManagerReviewed = 'manager_reviewed';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::ManagerReviewed => 'Manager Reviewed',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted => 'warning',
            self::ManagerReviewed => 'info',
            self::Closed => 'success',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Submitted,
            self::Submitted => $next === self::ManagerReviewed,
            self::ManagerReviewed => $next === self::Closed,
            self::Closed => false,
        };
    }
}
