<?php

namespace App\Support;

class Permissions
{
    public const ENTITIES = [
        'applications',
        'orders',
        'reservations',
        'customers',
        'vehicles',
        'invoices',
        'payments',
        'turnover',
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
