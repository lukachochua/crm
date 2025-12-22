<?php

namespace App\Policies\Hr;

use App\Models\Hr\Onboarding\OnboardingTemplateTask;
use App\Models\User;
use App\Support\Permissions;

class OnboardingTemplateTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'view'));
    }

    public function view(User $user, OnboardingTemplateTask $task): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'create'));
    }

    public function update(User $user, OnboardingTemplateTask $task): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'update'));
    }

    public function delete(User $user, OnboardingTemplateTask $task): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'export'));
    }

    public function restore(User $user, OnboardingTemplateTask $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, OnboardingTemplateTask $task): bool
    {
        return false;
    }
}
