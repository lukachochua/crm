<?php

namespace App\Filament\Resources\Hr\FeedbackRequestResource\RelationManagers;

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

class FeedbackAnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('feedback_question_id')
                    ->label('Question')
                    ->relationship('question', 'question_text')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('score')
                    ->numeric()
                    ->required(),
                Textarea::make('comment')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.question_text')
                    ->label('Question')
                    ->searchable(),
                TextColumn::make('score')
                    ->numeric(decimalPlaces: 2)
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
}
