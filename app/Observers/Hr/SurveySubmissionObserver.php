<?php

namespace App\Observers\Hr;

use App\Models\Hr\Survey\SurveySubmission;
use App\Observers\Concerns\LogsDeletion;

class SurveySubmissionObserver
{
    use LogsDeletion;
}
