<?php

namespace App\Policies\Hr;

use App\Models\Hr\Survey\SurveyAnswer;
use App\Models\User;
use App\Policies\Hr\Concerns\ScopesHrAccess;
use App\Support\Permissions;

class SurveyAnswerPolicy
{
    use ScopesHrAccess;

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('survey_submissions', 'view'));
    }

    public function view(User $user, SurveyAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('survey_submissions', 'view'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('submission.user');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->submission ? $this->scopedEmployeeByUserId($user, $answer->submission->user_id) : false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('survey_submissions', 'create'));
    }

    public function update(User $user, SurveyAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('survey_submissions', 'update'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('submission.user');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->submission ? $this->scopedEmployeeByUserId($user, $answer->submission->user_id) : false;
        }

        return true;
    }

    public function delete(User $user, SurveyAnswer $answer): bool
    {
        if (! $user->can(Permissions::permission('survey_submissions', 'delete'))) {
            return false;
        }

        if ($this->canBypassScope($user)) {
            return true;
        }

        $answer->loadMissing('submission.user');
        if ($this->isHrManager($user) || $this->isDepartmentManager($user)) {
            return $answer->submission ? $this->scopedEmployeeByUserId($user, $answer->submission->user_id) : false;
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('survey_submissions', 'export'));
    }

    public function restore(User $user, SurveyAnswer $answer): bool
    {
        return false;
    }

    public function forceDelete(User $user, SurveyAnswer $answer): bool
    {
        return false;
    }
}
