<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\TrainingAttendanceStatus;
use App\Enums\Hr\TrainingResultStatus;
use App\Filament\Resources\Hr\TrainingParticipantResource\Pages;
use App\Models\Hr\Employee;
use App\Models\Hr\Training\TrainingParticipant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class TrainingParticipantResource extends Resource
{
    protected static ?string $model = TrainingParticipant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 51;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('training_session_id')
                    ->label('Session')
                    ->relationship('session', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record): string => $record->user?->name ?? ('Employee #' . $record->id))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('attendance_status')
                    ->options(static::attendanceOptions())
                    ->required(),
                Select::make('result_status')
                    ->options(static::resultOptions())
                    ->nullable(),
                TextInput::make('result_score')
                    ->numeric()
                    ->nullable(),
                DateTimePicker::make('completed_at')
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.title')
                    ->label('Session')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('attendance_status')
                    ->badge()
                    ->formatStateUsing(fn (TrainingAttendanceStatus|string $state): string => ($state instanceof TrainingAttendanceStatus ? $state : TrainingAttendanceStatus::from($state))->label())
                    ->color(fn (TrainingAttendanceStatus|string $state): string => ($state instanceof TrainingAttendanceStatus ? $state : TrainingAttendanceStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('result_status')
                    ->badge()
                    ->formatStateUsing(fn (TrainingResultStatus|string|null $state): string => $state ? ($state instanceof TrainingResultStatus ? $state : TrainingResultStatus::from($state))->label() : '—')
                    ->color(fn (TrainingResultStatus|string|null $state): string => $state ? ($state instanceof TrainingResultStatus ? $state : TrainingResultStatus::from($state))->color() : 'gray'),
                TextColumn::make('result_score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (TrainingParticipant $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (TrainingParticipant $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new TrainingParticipant())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingParticipants::route('/'),
            'create' => Pages\CreateTrainingParticipant::route('/create'),
            'view' => Pages\ViewTrainingParticipant::route('/{record}'),
            'edit' => Pages\EditTrainingParticipant::route('/{record}/edit'),
        ];
    }

    protected static function attendanceOptions(): array
    {
        $options = [];

        foreach (TrainingAttendanceStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    protected static function resultOptions(): array
    {
        $options = [];

        foreach (TrainingResultStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
