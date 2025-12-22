<?php

namespace App\Enums\Hr;

enum RaterType: string
{
    case Self = 'self';
    case Manager = 'manager';
    case Peer = 'peer';

    public function label(): string
    {
        return match ($this) {
            self::Self => 'Self',
            self::Manager => 'Manager',
            self::Peer => 'Peer',
        };
    }
}
