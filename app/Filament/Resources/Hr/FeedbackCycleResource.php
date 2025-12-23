<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\FeedbackCycleStatus;
use App\Filament\Resources\Hr\FeedbackCycleResource\Pages;
use App\Filament\Resources\Hr\FeedbackCycleResource\RelationManagers\FeedbackQuestionsRelationManager;
use App\Models\Hr\Feedback\FeedbackCycle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class FeedbackCycleResource extends HrResource
{
    protected static ?string $model = FeedbackCycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $hrNavigationGroup = 'Feedback';

    protected static ?int $navigationSort = 80;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period_start')
                    ->date()
                    ->sortable(),
                TextColumn::make('period_end')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (FeedbackCycleStatus|string $state): string => ($state instanceof FeedbackCycleStatus ? $state : FeedbackCycleStatus::from($state))->label())
                    ->color(fn (FeedbackCycleStatus|string $state): string => ($state instanceof FeedbackCycleStatus ? $state : FeedbackCycleStatus::from($state))->color())
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (FeedbackCycle $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (FeedbackCycle $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new FeedbackCycle())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FeedbackQuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbackCycles::route('/'),
            'create' => Pages\CreateFeedbackCycle::route('/create'),
            'view' => Pages\ViewFeedbackCycle::route('/{record}'),
            'edit' => Pages\EditFeedbackCycle::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (FeedbackCycleStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
