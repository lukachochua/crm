<?php

namespace App\Enums\Hr;

use App\Enums\Concerns\HasStatusTransitions;

enum TrainingResultStatus: string
{
    use HasStatusTransitions;

    case Pending = 'pending';
    case Passed = 'passed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Passed => 'Passed',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Passed => 'success',
            self::Failed => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Pending => in_array($next, [self::Passed, self::Failed], true),
            self::Passed, self::Failed => false,
        };
    }
}
