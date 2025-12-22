<?php

namespace App\Policies\Hr;

use App\Models\Hr\Feedback\FeedbackQuestion;
use App\Models\User;
use App\Support\Permissions;

class FeedbackQuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'view'));
    }

    public function view(User $user, FeedbackQuestion $question): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'create'));
    }

    public function update(User $user, FeedbackQuestion $question): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'update'));
    }

    public function delete(User $user, FeedbackQuestion $question): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_cycles', 'export'));
    }

    public function restore(User $user, FeedbackQuestion $question): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeedbackQuestion $question): bool
    {
        return false;
    }
}
