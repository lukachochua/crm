<?php

namespace App\Policies\Hr;

use App\Models\Hr\Recruitment\Candidate;
use App\Models\User;
use App\Support\Permissions;

class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('candidates', 'view'));
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return $user->can(Permissions::permission('candidates', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('candidates', 'create'));
    }

    public function update(User $user, Candidate $candidate): bool
    {
        return $user->can(Permissions::permission('candidates', 'update'));
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->can(Permissions::permission('candidates', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('candidates', 'export'));
    }

    public function restore(User $user, Candidate $candidate): bool
    {
        return false;
    }

    public function forceDelete(User $user, Candidate $candidate): bool
    {
        return false;
    }
}
