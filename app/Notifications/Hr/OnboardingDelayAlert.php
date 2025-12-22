<?php

namespace App\Notifications\Hr;

use App\Models\Hr\Onboarding\EmployeeOnboarding;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OnboardingDelayAlert extends Notification
{
    use Queueable;

    public function __construct(
        public readonly EmployeeOnboarding $onboarding,
        public readonly int $daysOverdue
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'employee_id' => $this->onboarding->employee_id,
            'onboarding_id' => $this->onboarding->id,
            'days_overdue' => $this->daysOverdue,
            'due_date' => optional($this->onboarding->due_date)->toDateString(),
        ];
    }
}
