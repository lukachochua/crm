<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Enums\Hr\EmployeeStatus;
use App\Enums\Hr\SurveyStatus;
use App\Models\Hr\Employee;
use App\Models\Hr\Survey\EngagementSurvey;
use App\Notifications\Hr\SurveyOpenCloseNotification;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Notification;

class EngagementSurveyObserver
{
    use LogsDeletion;

    public function updated(EngagementSurvey $survey): void
    {
        if (! $survey->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $survey,
            AuditActionType::StatusChange,
            $survey->getOriginal(),
            $survey->getAttributes()
        );

        $status = $survey->status instanceof SurveyStatus
            ? $survey->status
            : SurveyStatus::from($survey->status);

        if (! in_array($status, [SurveyStatus::Open, SurveyStatus::Closed], true)) {
            return;
        }

        $recipients = Employee::query()
            ->where('status', EmployeeStatus::Active->value)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new SurveyOpenCloseNotification($survey, $status));
    }
}
