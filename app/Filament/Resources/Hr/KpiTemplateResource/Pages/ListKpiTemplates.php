<?php

namespace App\Filament\Resources\Hr\KpiTemplateResource\Pages;

use App\Filament\Resources\Hr\KpiTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiTemplates extends ListRecords
{
    protected static string $resource = KpiTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
