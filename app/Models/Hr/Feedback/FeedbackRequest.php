<?php

namespace App\Models\Hr\Feedback;

use App\Enums\Hr\FeedbackRequestStatus;
use App\Enums\Hr\RaterType;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\Hr\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackRequest extends Model
{
    use EnforcesStatusTransitions;

    protected $fillable = [
        'feedback_cycle_id',
        'employee_id',
        'rater_user_id',
        'rater_type',
        'status',
        'requested_at',
        'submitted_at',
    ];

    protected $casts = [
        'rater_type' => RaterType::class,
        'status' => FeedbackRequestStatus::class,
        'requested_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(FeedbackCycle::class, 'feedback_cycle_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FeedbackAnswer::class);
    }

    protected static function statusEnumClass(): string
    {
        return FeedbackRequestStatus::class;
    }
}
