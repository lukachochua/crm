<?php

namespace App\Enums;

enum AuditActionType: string
{
    case StatusChange = 'status_change';
    case FinancialAction = 'financial_action';
    case Deletion = 'deletion';

    public function label(): string
    {
        return match ($this) {
            self::StatusChange => 'Status Change',
            self::FinancialAction => 'Financial Action',
            self::Deletion => 'Deletion',
        };
    }
}
