<?php

namespace App\Enums\Hr;

enum QuestionType: string
{
    case Scale = 'scale';
    case SingleChoice = 'single_choice';
    case MultiChoice = 'multi_choice';
    case Text = 'text';

    public function label(): string
    {
        return match ($this) {
            self::Scale => 'Scale',
            self::SingleChoice => 'Single Choice',
            self::MultiChoice => 'Multi Choice',
            self::Text => 'Text',
        };
    }
}
