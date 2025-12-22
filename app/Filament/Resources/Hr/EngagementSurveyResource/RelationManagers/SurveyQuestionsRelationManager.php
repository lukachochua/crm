<?php

namespace App\Filament\Resources\Hr\EngagementSurveyResource\RelationManagers;

use App\Enums\Hr\QuestionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurveyQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('question_text')
                    ->required()
                    ->maxLength(255),
                Select::make('question_type')
                    ->options(static::typeOptions())
                    ->required(),
                Textarea::make('config')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('sort_order')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question_text')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question_type')
                    ->formatStateUsing(fn (QuestionType|string $state): string => ($state instanceof QuestionType ? $state : QuestionType::from($state))->label())
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected static function typeOptions(): array
    {
        $options = [];

        foreach (QuestionType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }
}
