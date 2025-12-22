<?php

namespace App\Policies\Hr;

use App\Models\Hr\Training\TrainingSession;
use App\Models\User;
use App\Support\Permissions;

class TrainingSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'view'));
    }

    public function view(User $user, TrainingSession $session): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'create'));
    }

    public function update(User $user, TrainingSession $session): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'update'));
    }

    public function delete(User $user, TrainingSession $session): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('training_sessions', 'export'));
    }

    public function restore(User $user, TrainingSession $session): bool
    {
        return false;
    }

    public function forceDelete(User $user, TrainingSession $session): bool
    {
        return false;
    }
}
