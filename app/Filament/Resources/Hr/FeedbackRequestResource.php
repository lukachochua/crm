<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\FeedbackRequestStatus;
use App\Enums\Hr\RaterType;
use App\Filament\Resources\Hr\FeedbackRequestResource\Pages;
use App\Filament\Resources\Hr\FeedbackRequestResource\RelationManagers\FeedbackAnswersRelationManager;
use App\Models\Hr\Employee;
use App\Models\Hr\Feedback\FeedbackRequest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class FeedbackRequestResource extends Resource
{
    protected static ?string $model = FeedbackRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 81;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('feedback_cycle_id')
                    ->label('Cycle')
                    ->relationship('cycle', 'name')
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
                Select::make('rater_user_id')
                    ->label('Rater')
                    ->relationship('rater', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('rater_type')
                    ->options(static::raterOptions())
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DateTimePicker::make('requested_at')
                    ->required(),
                DateTimePicker::make('submitted_at')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cycle.name')
                    ->label('Cycle')
                    ->sortable(),
                TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rater.name')
                    ->label('Rater')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rater_type')
                    ->formatStateUsing(fn (RaterType|string $state): string => ($state instanceof RaterType ? $state : RaterType::from($state))->label())
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (FeedbackRequestStatus|string $state): string => ($state instanceof FeedbackRequestStatus ? $state : FeedbackRequestStatus::from($state))->label())
                    ->color(fn (FeedbackRequestStatus|string $state): string => ($state instanceof FeedbackRequestStatus ? $state : FeedbackRequestStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (FeedbackRequest $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (FeedbackRequest $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new FeedbackRequest())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FeedbackAnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbackRequests::route('/'),
            'create' => Pages\CreateFeedbackRequest::route('/create'),
            'view' => Pages\ViewFeedbackRequest::route('/{record}'),
            'edit' => Pages\EditFeedbackRequest::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (FeedbackRequestStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    protected static function raterOptions(): array
    {
        $options = [];

        foreach (RaterType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }
}
