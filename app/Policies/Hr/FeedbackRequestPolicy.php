<?php

namespace App\Policies\Hr;

use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class FeedbackRequestPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'view'));
    }

    public function view(User $user, FeedbackRequest $request): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $request->employee);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'create'));
    }

    public function update(User $user, FeedbackRequest $request): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $request->employee);
        }

        return true;
    }

    public function delete(User $user, FeedbackRequest $request): bool
    {
        if (! $user->can(Permissions::permission('feedback_requests', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $this->scopedEmployee($user, $request->employee);
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('feedback_requests', 'export'));
    }

    public function restore(User $user, FeedbackRequest $request): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeedbackRequest $request): bool
    {
        return false;
    }
}
