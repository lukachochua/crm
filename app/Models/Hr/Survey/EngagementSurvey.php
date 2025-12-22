<?php

namespace App\Models\Hr\Survey;

use App\Enums\Hr\SurveyStatus;
use App\Models\Concerns\AssignsCreator;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EngagementSurvey extends Model
{
    use AssignsCreator;
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'opens_at',
        'closes_at',
        'created_by',
    ];

    protected $casts = [
        'status' => SurveyStatus::class,
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SurveySubmission::class);
    }

    protected static function statusEnumClass(): string
    {
        return SurveyStatus::class;
    }
}
