<?php

namespace App\Models\Hr\Feedback;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackAnswer extends Model
{
    protected $fillable = [
        'feedback_request_id',
        'feedback_question_id',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(FeedbackRequest::class, 'feedback_request_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(FeedbackQuestion::class, 'feedback_question_id');
    }
}
