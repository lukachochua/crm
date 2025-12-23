<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\SurveyStatus;
use App\Filament\Resources\Hr\EngagementSurveyResource\Pages;
use App\Filament\Resources\Hr\EngagementSurveyResource\RelationManagers\SurveyQuestionsRelationManager;
use App\Filament\Resources\Hr\EngagementSurveyResource\RelationManagers\SurveySubmissionsRelationManager;
use App\Models\Hr\Survey\EngagementSurvey;
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

class EngagementSurveyResource extends HrResource
{
    protected static ?string $model = EngagementSurvey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $hrNavigationGroup = 'Surveys';

    protected static ?int $navigationSort = 90;

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
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DateTimePicker::make('opens_at')
                    ->nullable(),
                DateTimePicker::make('closes_at')
                    ->nullable(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (SurveyStatus|string $state): string => ($state instanceof SurveyStatus ? $state : SurveyStatus::from($state))->label())
                    ->color(fn (SurveyStatus|string $state): string => ($state instanceof SurveyStatus ? $state : SurveyStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('opens_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('closes_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (EngagementSurvey $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (EngagementSurvey $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new EngagementSurvey())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SurveyQuestionsRelationManager::class,
            SurveySubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEngagementSurveys::route('/'),
            'create' => Pages\CreateEngagementSurvey::route('/create'),
            'view' => Pages\ViewEngagementSurvey::route('/{record}'),
            'edit' => Pages\EditEngagementSurvey::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (SurveyStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
