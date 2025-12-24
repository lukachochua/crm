<?php

namespace App\Support;

class Permissions
{
    public const ENTITIES = [
        'applications',
        'orders',
        'reservations',
        'customers',
        'customer_contracts',
        'customer_pricing_profiles',
        'vehicles',
        'invoices',
        'payments',
        'internal_transfers',
        'customer_returns',
        'turnover',
        'departments',
        'positions',
        'branches',
        'contract_types',
        'employees',
        'employee_documents',
        'kpi_templates',
        'kpi_cycles',
        'kpi_reports',
        'training_sessions',
        'training_participants',
        'candidates',
        'onboarding_templates',
        'employee_onboardings',
        'feedback_cycles',
        'feedback_requests',
        'engagement_surveys',
        'survey_submissions',
    ];

    public const ACTIONS = [
        'view',
        'create',
        'update',
        'delete',
        'export',
    ];

    public static function permission(string $entity, string $action): string
    {
        return $entity . '.' . $action;
    }

    public static function all(): array
    {
        $permissions = [];

        foreach (self::ENTITIES as $entity) {
            foreach (self::ACTIONS as $action) {
                $permissions[] = self::permission($entity, $action);
            }
        }

        return $permissions;
    }
}
