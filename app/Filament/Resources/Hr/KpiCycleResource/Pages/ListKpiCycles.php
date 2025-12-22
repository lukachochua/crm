<?php

namespace App\Filament\Resources\Hr\KpiCycleResource\Pages;

use App\Filament\Resources\Hr\KpiCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiCycles extends ListRecords
{
    protected static string $resource = KpiCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
