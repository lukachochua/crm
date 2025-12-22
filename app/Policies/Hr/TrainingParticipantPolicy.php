<?php

namespace App\Policies\Hr;

use App\Models\Hr\Training\TrainingParticipant;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class TrainingParticipantPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('training_participants', 'view'));
    }

    public function view(User $user, TrainingParticipant $participant): bool
    {
        if (! $user->can(Permissions::permission('training_participants', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $participant->employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('training_participants', 'create'));
    }

    public function update(User $user, TrainingParticipant $participant): bool
    {
        if (! $user->can(Permissions::permission('training_participants', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $participant->employee);
        }

        return true;
    }

    public function delete(User $user, TrainingParticipant $participant): bool
    {
        if (! $user->can(Permissions::permission('training_participants', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $participant->employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('training_participants', 'export'));
    }

    public function restore(User $user, TrainingParticipant $participant): bool
    {
        return false;
    }

    public function forceDelete(User $user, TrainingParticipant $participant): bool
    {
        return false;
    }
}
