<?php

namespace App\Models\Hr\Feedback;

use App\Enums\Hr\FeedbackCycleStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackCycle extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'period_start',
        'period_end',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'status' => FeedbackCycleStatus::class,
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(FeedbackQuestion::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }

    protected static function statusEnumClass(): string
    {
        return FeedbackCycleStatus::class;
    }
}
