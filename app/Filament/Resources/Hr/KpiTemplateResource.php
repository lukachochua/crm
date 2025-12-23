<?php

namespace App\Filament\Resources\Hr;

use App\Filament\Resources\Hr\KpiTemplateResource\Pages;
use App\Filament\Resources\Hr\KpiTemplateResource\RelationManagers\KpiTemplateItemsRelationManager;
use App\Models\Hr\Kpi\KpiTemplate;
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

class KpiTemplateResource extends HrResource
{
    protected static ?string $model = KpiTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $hrNavigationGroup = 'Performance';

    protected static ?int $navigationSort = 40;

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
                Select::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                    ->visible(fn (KpiTemplate $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (KpiTemplate $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new KpiTemplate())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            KpiTemplateItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiTemplates::route('/'),
            'create' => Pages\CreateKpiTemplate::route('/create'),
            'view' => Pages\ViewKpiTemplate::route('/{record}'),
            'edit' => Pages\EditKpiTemplate::route('/{record}/edit'),
        ];
    }
}
