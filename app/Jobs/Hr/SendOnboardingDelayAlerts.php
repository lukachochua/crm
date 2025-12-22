<?php

namespace App\Jobs\Hr;

use App\Enums\Hr\OnboardingStatus;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\User;
use App\Notifications\Hr\OnboardingDelayAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendOnboardingDelayAlerts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $today = now()->startOfDay();

        $overdue = EmployeeOnboarding::query()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->whereIn('status', [OnboardingStatus::NotStarted->value, OnboardingStatus::InProgress->value])
            ->with('employee')
            ->get();

        if ($overdue->isEmpty()) {
            return;
        }

        $hrRecipients = User::role(['superadmin', 'hr_admin', 'hr_manager'])->get();

        foreach ($overdue as $onboarding) {
            $recipients = $hrRecipients->values();

            if ($onboarding->employee && $onboarding->employee->manager_user_id) {
                $manager = User::query()->find($onboarding->employee->manager_user_id);
                if ($manager) {
                    $recipients->push($manager);
                }
            }

            $recipients = $recipients->unique('id');
            if ($recipients->isEmpty()) {
                continue;
            }

            $daysOverdue = $onboarding->due_date->diffInDays($today);
            Notification::send($recipients, new OnboardingDelayAlert($onboarding, $daysOverdue));
        }
    }
}
