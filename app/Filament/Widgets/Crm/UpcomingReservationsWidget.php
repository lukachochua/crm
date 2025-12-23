<?php

namespace App\Filament\Widgets\Crm;

use App\Enums\Crm\ReservationStatus;
use App\Filament\Resources\Crm\ReservationResource;
use App\Models\Crm\Sales\Reservation;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class UpcomingReservationsWidget extends TableWidget
{
    protected static ?int $sort = -12;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Upcoming Reservations')
            ->query(
                Reservation::query()
                    ->with(['order', 'vehicle'])
                    ->where('reserved_until', '>=', now())
                    ->where('status', ReservationStatus::Active->value)
                    ->orderBy('reserved_from')
            )
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order'),
                TextColumn::make('vehicle.vin_or_serial')
                    ->label('Vehicle'),
                TextColumn::make('reserved_from')
                    ->dateTime()
                    ->label('From'),
                TextColumn::make('reserved_until')
                    ->dateTime()
                    ->label('Until'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ReservationStatus|string $state): string => ($state instanceof ReservationStatus ? $state : ReservationStatus::from($state))->label())
                    ->color(fn (ReservationStatus|string $state): string => ($state instanceof ReservationStatus ? $state : ReservationStatus::from($state))->color()),
            ])
            ->recordUrl(fn (Reservation $record): string => ReservationResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
