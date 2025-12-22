<?php

namespace App\Models\Hr\Survey;

use App\Enums\Hr\QuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'engagement_survey_id',
        'question_text',
        'question_type',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'question_type' => QuestionType::class,
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(EngagementSurvey::class, 'engagement_survey_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
