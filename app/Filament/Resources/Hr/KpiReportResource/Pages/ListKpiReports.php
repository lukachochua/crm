<?php

namespace App\Filament\Resources\Hr\KpiReportResource\Pages;

use App\Filament\Resources\Hr\KpiReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiReports extends ListRecords
{
    protected static string $resource = KpiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
