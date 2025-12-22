<?php

namespace App\Filament\Resources\Hr\EmployeeOnboardingResource\RelationManagers;

use App\Enums\Hr\OnboardingTaskStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeOnboardingTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('onboarding_template_task_id')
                    ->label('Template Task')
                    ->relationship('templateTask', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('assigned_to_user_id')
                    ->label('Assigned To')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DatePicker::make('due_date')
                    ->nullable(),
                DateTimePicker::make('completed_at')
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('templateTask.title')
                    ->label('Task')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OnboardingTaskStatus|string $state): string => ($state instanceof OnboardingTaskStatus ? $state : OnboardingTaskStatus::from($state))->label())
                    ->color(fn (OnboardingTaskStatus|string $state): string => ($state instanceof OnboardingTaskStatus ? $state : OnboardingTaskStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
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

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (OnboardingTaskStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
