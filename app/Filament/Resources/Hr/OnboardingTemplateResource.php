<?php

namespace App\Filament\Resources\Hr;

use App\Filament\Resources\Hr\OnboardingTemplateResource\Pages;
use App\Filament\Resources\Hr\OnboardingTemplateResource\RelationManagers\OnboardingTemplateTasksRelationManager;
use App\Models\Hr\Onboarding\OnboardingTemplate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class OnboardingTemplateResource extends HrResource
{
    protected static ?string $model = OnboardingTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $hrNavigationGroup = 'Onboarding';

    protected static ?int $navigationSort = 70;

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
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Toggle::make('is_active')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),
                TextColumn::make('position.name')
                    ->label('Position')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (OnboardingTemplate $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (OnboardingTemplate $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new OnboardingTemplate())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OnboardingTemplateTasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOnboardingTemplates::route('/'),
            'create' => Pages\CreateOnboardingTemplate::route('/create'),
            'view' => Pages\ViewOnboardingTemplate::route('/{record}'),
            'edit' => Pages\EditOnboardingTemplate::route('/{record}/edit'),
        ];
    }
}
