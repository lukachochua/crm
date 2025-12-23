<?php

namespace App\Filament\Resources\Crm;

use App\Enums\OrderStatus;
use App\Filament\Exports\Crm\OrderExporter;
use App\Filament\Resources\Crm\OrderResource\Pages;
use App\Filament\Resources\Crm\OrderResource\RelationManagers\InvoicesRelationManager;
use App\Models\Crm\Application;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn (Customer $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('application_id')
                    ->label('Application')
                    ->relationship('application', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Application $record): string => 'Application #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                TextInput::make('discount_amount')
                    ->numeric()
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
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (Order $record): string => $record->customer->first_name . ' ' . $record->customer->last_name)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->label())
                    ->color(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class)
                    ->visible(fn (): bool => Gate::allows('export', Order::class)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Order $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Order $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new Order())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (OrderStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
