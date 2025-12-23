<?php

namespace App\Filament\Resources\Crm;

use App\Enums\Crm\ReservationStatus;
use App\Filament\Exports\Crm\ReservationExporter;
use App\Filament\Resources\Crm\ReservationResource\Pages;
use App\Models\Crm\Sales\Reservation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'vin_or_serial')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DateTimePicker::make('reserved_from')
                    ->required(),
                DateTimePicker::make('reserved_until')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vehicle.vin_or_serial')
                    ->label('Vehicle')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ReservationStatus|string $state): string => ($state instanceof ReservationStatus ? $state : ReservationStatus::from($state))->label())
                    ->color(fn (ReservationStatus|string $state): string => ($state instanceof ReservationStatus ? $state : ReservationStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('reserved_from')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('reserved_until')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
                Filter::make('reserved_from')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('reserved_from', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('reserved_from', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ReservationExporter::class)
                    ->visible(fn (): bool => Gate::allows('export', Reservation::class)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Reservation $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Reservation $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new Reservation())),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (ReservationStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
