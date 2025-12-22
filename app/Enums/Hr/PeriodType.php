<?php

namespace App\Enums\Hr;

enum PeriodType: string
{
    case Month = 'month';
    case Quarter = 'quarter';

    public function label(): string
    {
        return match ($this) {
            self::Month => 'Month',
            self::Quarter => 'Quarter',
        };
    }
}
