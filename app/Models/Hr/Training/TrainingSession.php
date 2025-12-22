<?php

namespace App\Models\Hr\Training;

use App\Enums\Hr\TrainingSessionStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingSession extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'trainer_user_id',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => TrainingSessionStatus::class,
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    protected static function statusEnumClass(): string
    {
        return TrainingSessionStatus::class;
    }
}
