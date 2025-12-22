<?php

namespace App\Policies\Hr;

use App\Models\Hr\Feedback\FeedbackAnswer;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class FeedbackAnswerPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'view'));
    }

    public function view(User $user, FeedbackAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('request.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->request ? $this->scopedEmployee($user, $answer->request->employee) : false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'create'));
    }

    public function update(User $user, FeedbackAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('request.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->request ? $this->scopedEmployee($user, $answer->request->employee) : false;
        }

        return true;
    }

    public function delete(User $user, FeedbackAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('request.employee');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->request ? $this->scopedEmployee($user, $answer->request->employee) : false;
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'export'));
    }

    public function restore(User $user, FeedbackAnswer $answer): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeedbackAnswer $answer): bool
    {
        return false;
    }
}
