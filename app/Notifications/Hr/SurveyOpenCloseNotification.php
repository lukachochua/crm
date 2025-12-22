<?php

namespace App\Notifications\Hr;

use App\Enums\Hr\SurveyStatus;
use App\Models\Hr\Survey\EngagementSurvey;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SurveyOpenCloseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly EngagementSurvey $survey,
        public readonly SurveyStatus $status
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'survey_id' => $this->survey->id,
            'title' => $this->survey->title,
            'status' => $this->status->value,
        ];
    }
}
