<?php

namespace App\Policies\Hr;

use App\Models\Hr\EmployeeDocument;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class EmployeeDocumentPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('employee_documents', 'view'));
    }

    public function view(User $user, EmployeeDocument $document): bool
    {
        if (! $user->can(Permissions::permission('employee_documents', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $document->employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('employee_documents', 'create'));
    }

    public function update(User $user, EmployeeDocument $document): bool
    {
        if (! $user->can(Permissions::permission('employee_documents', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $document->employee);
        }

        return true;
    }

    public function delete(User $user, EmployeeDocument $document): bool
    {
        if (! $user->can(Permissions::permission('employee_documents', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $document->employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('employee_documents', 'export'));
    }

    public function restore(User $user, EmployeeDocument $document): bool
    {
        return false;
    }

    public function forceDelete(User $user, EmployeeDocument $document): bool
    {
        return false;
    }
}
