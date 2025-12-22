<?php

namespace App\Policies\Hr;

use App\Models\Hr\Feedback\FeedbackCycle;
use App\Models\User;
use App\Support\Permissions;

class FeedbackCyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'view'));
    }

    public function view(User $user, FeedbackCycle $cycle): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'create'));
    }

    public function update(User $user, FeedbackCycle $cycle): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'update'));
    }

    public function delete(User $user, FeedbackCycle $cycle): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'export'));
    }

    public function restore(User $user, FeedbackCycle $cycle): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeedbackCycle $cycle): bool
    {
        return false;
    }
}
