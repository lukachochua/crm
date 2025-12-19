<?php

namespace App\Enums;

use App\Enums\Concerns\HasStatusTransitions;

enum InvoiceStatus: string
{
    use HasStatusTransitions;

    case Draft = 'draft';
    case Issued = 'issued';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Issued => 'Issued',
            self::PartiallyPaid => 'Partially Paid',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Issued => 'info',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::Issued,
            self::Issued => in_array($next, [self::PartiallyPaid, self::Paid, self::Cancelled], true),
            self::PartiallyPaid => $next === self::Paid,
            self::Paid, self::Cancelled => false,
        };
    }
}
