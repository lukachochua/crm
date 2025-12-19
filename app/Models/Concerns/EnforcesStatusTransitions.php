<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait EnforcesStatusTransitions
{
    protected static function bootEnforcesStatusTransitions(): void
    {
        static::updating(function (Model $model): void {
            if (! $model->isDirty('status')) {
                return;
            }

            $originalStatus = $model->getOriginal('status');
            if ($originalStatus === null) {
                return;
            }

            $enumClass = static::statusEnumClass();
            $from = $originalStatus instanceof $enumClass
                ? $originalStatus
                : $enumClass::from($originalStatus);
            $to = $model->status instanceof $enumClass
                ? $model->status
                : $enumClass::from($model->status);

            $from->assertCanTransitionTo($to);
        });
    }

    abstract protected static function statusEnumClass(): string;
}
