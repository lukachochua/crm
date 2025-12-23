<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\TrainingSessionStatus;
use App\Filament\Resources\Hr\TrainingSessionResource\Pages;
use App\Models\Hr\Training\TrainingSession;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class TrainingSessionResource extends HrResource
{
    protected static ?string $model = TrainingSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $hrNavigationGroup = 'Training';

    protected static ?int $navigationSort = 50;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required(),
                TextInput::make('location')
                    ->maxLength(255)
                    ->nullable(),
                Select::make('trainer_user_id')
                    ->label('Trainer')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (TrainingSessionStatus|string $state): string => ($state instanceof TrainingSessionStatus ? $state : TrainingSessionStatus::from($state))->label())
                    ->color(fn (TrainingSessionStatus|string $state): string => ($state instanceof TrainingSessionStatus ? $state : TrainingSessionStatus::from($state))->color())
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (TrainingSession $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (TrainingSession $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new TrainingSession())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingSessions::route('/'),
            'create' => Pages\CreateTrainingSession::route('/create'),
            'view' => Pages\ViewTrainingSession::route('/{record}'),
            'edit' => Pages\EditTrainingSession::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (TrainingSessionStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
