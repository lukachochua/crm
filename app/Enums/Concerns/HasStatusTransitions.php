<?php

namespace App\Enums\Concerns;

use Illuminate\Validation\ValidationException;

trait HasStatusTransitions
{
    public function assertCanTransitionTo(self $next): void
    {
        if (! $this->canTransitionTo($next)) {
            $message = sprintf(
                'Invalid status transition from %s to %s.',
                $this->value,
                $next->value
            );

            throw ValidationException::withMessages([
                'status' => $message,
                'data.status' => $message,
            ]);
        }
    }

    abstract public function canTransitionTo(self $next): bool;
}
