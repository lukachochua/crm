<?php

namespace App\Filament\Exports\Crm;

use App\Enums\Crm\ApplicationStatus;
use App\Models\Crm\Sales\Application;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ApplicationExporter extends Exporter
{
    protected static ?string $model = Application::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('customer_id'),
            ExportColumn::make('customer.first_name')
                ->label('Customer First Name'),
            ExportColumn::make('customer.last_name')
                ->label('Customer Last Name'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (ApplicationStatus|string $state): string => ($state instanceof ApplicationStatus ? $state : ApplicationStatus::from($state))->label()),
            ExportColumn::make('requested_at'),
            ExportColumn::make('created_by'),
            ExportColumn::make('description'),
            ExportColumn::make('source'),
            ExportColumn::make('internal_notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['customer']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your application export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
