<?php

namespace App\Filament\Resources\Crm;

use App\Enums\Crm\CustomerReturnStatus;
use App\Filament\Resources\Crm\CustomerReturnResource\Pages;
use App\Filament\Resources\Crm\CustomerReturnResource\RelationManagers\CustomerReturnItemsRelationManager;
use App\Models\Crm\Operations\CustomerReturn;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomerReturnResource extends Resource
{
    protected static ?string $model = CustomerReturn::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Select::make('customer_id')
                    ->relationship('customer', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('reported_by')
                    ->relationship('reportedBy', 'name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record): string => '<span style="white-space: nowrap;">' . e($record->name) . '</span>'
                    )
                    ->allowHtml()
                    ->default(fn (): ?int => Auth::id())
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('received_at')
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->default(CustomerReturnStatus::Draft->value)
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (?string $state, CustomerReturn $record): string => $record->customer
                        ? $record->customer->first_name . ' ' . $record->customer->last_name
                        : '-')
                    ->searchable(),
                TextColumn::make('reportedBy.name')
                    ->label('Reported by')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CustomerReturnStatus|string $state): string => ($state instanceof CustomerReturnStatus ? $state : CustomerReturnStatus::from($state))->label())
                    ->color(fn (CustomerReturnStatus|string $state): string => ($state instanceof CustomerReturnStatus ? $state : CustomerReturnStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (CustomerReturn $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (CustomerReturn $record): bool => Gate::allows('delete', $record)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CustomerReturnItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerReturns::route('/'),
            'create' => Pages\CreateCustomerReturn::route('/create'),
            'view' => Pages\ViewCustomerReturn::route('/{record}'),
            'edit' => Pages\EditCustomerReturn::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (CustomerReturnStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
