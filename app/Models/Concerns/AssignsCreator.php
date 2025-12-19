<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

trait AssignsCreator
{
    protected static function bootAssignsCreator(): void
    {
        static::creating(function (Model $model): void {
            if (! isset($model->created_by)) {
                $model->created_by = Auth::id();
            }

            if (! $model->created_by) {
                throw new RuntimeException('created_by must be set for this model.');
            }
        });
    }
}
