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
            'superadmin' => Permissions::all(),
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
            'hr_admin' => array_merge(
                $this->fullAccess('departments'),
                $this->fullAccess('positions'),
                $this->fullAccess('branches'),
                $this->fullAccess('contract_types'),
                $this->fullAccess('employees'),
                $this->fullAccess('employee_documents'),
                $this->fullAccess('kpi_templates'),
                $this->fullAccess('kpi_cycles'),
                $this->fullAccess('kpi_reports'),
                $this->fullAccess('training_sessions'),
                $this->fullAccess('training_participants'),
                $this->fullAccess('candidates'),
                $this->fullAccess('onboarding_templates'),
                $this->fullAccess('employee_onboardings'),
                $this->fullAccess('feedback_cycles'),
                $this->fullAccess('feedback_requests'),
                $this->fullAccess('engagement_surveys'),
                $this->fullAccess('survey_submissions')
            ),
            'hr_manager' => array_merge(
                $this->fullAccess('employees'),
                $this->fullAccess('employee_documents'),
                $this->fullAccess('kpi_cycles'),
                $this->fullAccess('kpi_reports'),
                $this->fullAccess('training_sessions'),
                $this->fullAccess('training_participants'),
                $this->fullAccess('candidates'),
                $this->fullAccess('employee_onboardings'),
                $this->fullAccess('feedback_requests'),
                $this->fullAccess('engagement_surveys'),
                $this->fullAccess('survey_submissions'),
                $this->viewAccess('departments'),
                $this->viewAccess('positions'),
                $this->viewAccess('branches'),
                $this->viewAccess('contract_types'),
                $this->viewAccess('kpi_templates'),
                $this->viewAccess('onboarding_templates'),
                $this->viewAccess('feedback_cycles')
            ),
            'department_manager' => array_merge(
                $this->viewAccess('departments'),
                $this->viewAccess('positions'),
                $this->viewAccess('branches'),
                $this->viewAccess('contract_types'),
                $this->viewAccess('employees'),
                $this->viewAccess('employee_documents'),
                $this->viewAccess('kpi_templates'),
                $this->viewAccess('kpi_cycles'),
                $this->viewAccess('training_sessions'),
                $this->viewAccess('candidates'),
                $this->viewAccess('onboarding_templates'),
                $this->viewAccess('feedback_cycles'),
                $this->viewAccess('engagement_surveys'),
                $this->viewUpdateAccess('kpi_reports'),
                $this->viewUpdateAccess('training_participants'),
                $this->viewUpdateAccess('employee_onboardings'),
                $this->viewUpdateAccess('feedback_requests'),
                $this->viewUpdateAccess('survey_submissions')
            ),
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

    private function viewUpdateAccess(string $entity): array
    {
        return [
            Permissions::permission($entity, 'view'),
            Permissions::permission($entity, 'update'),
        ];
    }
}
