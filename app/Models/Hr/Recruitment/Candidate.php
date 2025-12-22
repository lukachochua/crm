<?php

namespace App\Models\Hr\Recruitment;

use App\Enums\Hr\RecruitmentStage;
use App\Models\Hr\Branch;
use App\Models\Hr\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position_id',
        'branch_id',
        'stage',
        'applied_at',
        'source',
        'notes',
    ];

    protected $casts = [
        'stage' => RecruitmentStage::class,
        'applied_at' => 'datetime',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected static function booted(): void
    {
        static::updating(function (Candidate $candidate): void {
            if (! $candidate->isDirty('stage')) {
                return;
            }

            $originalStage = $candidate->getOriginal('stage');
            if ($originalStage === null) {
                return;
            }

            $from = $originalStage instanceof RecruitmentStage
                ? $originalStage
                : RecruitmentStage::from($originalStage);
            $to = $candidate->stage instanceof RecruitmentStage
                ? $candidate->stage
                : RecruitmentStage::from($candidate->stage);

            $from->assertCanTransitionTo($to);
        });
    }
}
