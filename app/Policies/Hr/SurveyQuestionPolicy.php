<?php

namespace App\Policies\Hr;

use App\Models\Hr\Survey\SurveyQuestion;
use App\Models\User;
use App\Support\Permissions;

class SurveyQuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'view'));
    }

    public function view(User $user, SurveyQuestion $question): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'create'));
    }

    public function update(User $user, SurveyQuestion $question): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'update'));
    }

    public function delete(User $user, SurveyQuestion $question): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('engagement_surveys', 'export'));
    }

    public function restore(User $user, SurveyQuestion $question): bool
    {
        return false;
    }

    public function forceDelete(User $user, SurveyQuestion $question): bool
    {
        return false;
    }
}
