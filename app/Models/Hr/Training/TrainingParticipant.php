<?php

namespace App\Models\Hr\Training;

use App\Enums\Hr\TrainingAttendanceStatus;
use App\Enums\Hr\TrainingResultStatus;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingParticipant extends Model
{
    protected $fillable = [
        'training_session_id',
        'employee_id',
        'attendance_status',
        'result_status',
        'result_score',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'attendance_status' => TrainingAttendanceStatus::class,
        'result_status' => TrainingResultStatus::class,
        'result_score' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted(): void
    {
        static::updating(function (TrainingParticipant $participant): void {
            if ($participant->isDirty('attendance_status')) {
                $original = $participant->getOriginal('attendance_status');
                if ($original !== null) {
                    $from = $original instanceof TrainingAttendanceStatus
                        ? $original
                        : TrainingAttendanceStatus::from($original);
                    $to = $participant->attendance_status instanceof TrainingAttendanceStatus
                        ? $participant->attendance_status
                        : TrainingAttendanceStatus::from($participant->attendance_status);
                    $from->assertCanTransitionTo($to);
                }
            }

            if ($participant->isDirty('result_status')) {
                $original = $participant->getOriginal('result_status');
                if ($original !== null) {
                    $from = $original instanceof TrainingResultStatus
                        ? $original
                        : TrainingResultStatus::from($original);
                    $to = $participant->result_status instanceof TrainingResultStatus
                        ? $participant->result_status
                        : TrainingResultStatus::from($participant->result_status);
                    $from->assertCanTransitionTo($to);
                }
            }
        });
    }
}
