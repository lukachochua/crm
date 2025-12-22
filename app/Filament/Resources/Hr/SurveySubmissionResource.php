<?php

namespace App\Filament\Resources\Hr;

use App\Filament\Resources\Hr\SurveySubmissionResource\Pages;
use App\Filament\Resources\Hr\SurveySubmissionResource\RelationManagers\SurveyAnswersRelationManager;
use App\Models\Hr\Survey\SurveySubmission;
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

class SurveySubmissionResource extends Resource
{
    protected static ?string $model = SurveySubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 91;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('engagement_survey_id')
                    ->label('Survey')
                    ->relationship('survey', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('submitted_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('survey.title')
                    ->label('Survey')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (SurveySubmission $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (SurveySubmission $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new SurveySubmission())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SurveyAnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveySubmissions::route('/'),
            'create' => Pages\CreateSurveySubmission::route('/create'),
            'view' => Pages\ViewSurveySubmission::route('/{record}'),
            'edit' => Pages\EditSurveySubmission::route('/{record}/edit'),
        ];
    }
}
