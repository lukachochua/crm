<?php

namespace App\Filament\Resources\Hr\SurveySubmissionResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurveyAnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('survey_question_id')
                    ->label('Question')
                    ->relationship('question', 'question_text')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('answer_value')
                    ->columnSpanFull()
                    ->rows(3)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.question_text')
                    ->label('Question')
                    ->searchable(),
                TextColumn::make('answer_value')
                    ->limit(40),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
