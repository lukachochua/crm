<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Permissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Sales' => array_merge(
                $this->fullAccess('applications'),
                $this->fullAccess('orders'),
                $this->fullAccess('reservations'),
                $this->fullAccess('customers'),
                $this->fullAccess('vehicles'),
                $this->viewAccess('invoices')
            ),
            'Back Office' => array_merge(
                $this->viewAccess('applications'),
                $this->fullAccess('orders'),
                $this->fullAccess('reservations'),
                $this->fullAccess('customers'),
                $this->fullAccess('vehicles'),
                $this->fullAccess('invoices'),
                $this->viewAccess('payments')
            ),
            'Finance' => array_merge(
                $this->viewAccess('orders'),
                $this->viewAccess('customers'),
                $this->fullAccess('invoices'),
                $this->fullAccess('payments'),
                $this->viewAccess('turnover')
            ),
            'Turnover' => array_merge(
                $this->viewAccess('invoices'),
                $this->fullAccess('turnover')
            ),
            'Admin' => Permissions::all(),
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }

    private function fullAccess(string $entity): array
    {
        return array_map(
            fn (string $action): string => Permissions::permission($entity, $action),
            Permissions::ACTIONS
        );
    }

    private function viewAccess(string $entity): array
    {
        return [Permissions::permission($entity, 'view')];
    }
}
