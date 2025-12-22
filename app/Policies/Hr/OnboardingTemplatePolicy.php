<?php

namespace App\Policies\Hr;

use App\Models\Hr\Onboarding\OnboardingTemplate;
use App\Models\User;
use App\Support\Permissions;

class OnboardingTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'view'));
    }

    public function view(User $user, OnboardingTemplate $template): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'view'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'create'));
    }

    public function update(User $user, OnboardingTemplate $template): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'update'));
    }

    public function delete(User $user, OnboardingTemplate $template): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'delete'));
    }

    public function export(User $user): bool
    {
        return $user->can(Permissions::permission('onboarding_templates', 'export'));
    }

    public function restore(User $user, OnboardingTemplate $template): bool
    {
        return false;
    }

    public function forceDelete(User $user, OnboardingTemplate $template): bool
    {
        return false;
    }
}
