<?php

namespace App\Filament\Resources\Hr\FeedbackCycleResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeedbackQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('question_text')
                    ->required()
                    ->maxLength(255),
                TextInput::make('weight')
                    ->numeric()
                    ->nullable(),
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
                TextColumn::make('weight')
                    ->numeric(decimalPlaces: 2)
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
}
