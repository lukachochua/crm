<?php

namespace App\Models\Hr\Feedback;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackQuestion extends Model
{
    protected $fillable = [
        'feedback_cycle_id',
        'question_text',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(FeedbackCycle::class, 'feedback_cycle_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FeedbackAnswer::class);
    }
}
