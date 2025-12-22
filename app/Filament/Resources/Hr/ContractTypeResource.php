<?php

namespace App\Filament\Resources\Hr;

use App\Filament\Resources\Hr\ContractTypeResource\Pages;
use App\Models\Hr\ContractType;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 23;

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
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
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
                TextColumn::make('code')
                    ->searchable()
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
                    ->visible(fn (ContractType $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (ContractType $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new ContractType())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTypes::route('/'),
            'create' => Pages\CreateContractType::route('/create'),
            'view' => Pages\ViewContractType::route('/{record}'),
            'edit' => Pages\EditContractType::route('/{record}/edit'),
        ];
    }
}
