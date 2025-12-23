<?php

namespace App\Filament\Exports\Crm;

use App\Models\Crm\TurnoverOverview;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TurnoverOverviewExporter extends Exporter
{
    protected static ?string $model = TurnoverOverview::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('period'),
            ExportColumn::make('total_invoiced'),
            ExportColumn::make('total_paid'),
            ExportColumn::make('outstanding_amount'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your turnover overview export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
