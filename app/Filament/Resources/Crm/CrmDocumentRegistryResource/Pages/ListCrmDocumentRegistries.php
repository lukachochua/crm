<?php

namespace App\Filament\Resources\Crm\CrmDocumentRegistryResource\Pages;

use App\Filament\Resources\Crm\CrmDocumentRegistryResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmDocumentRegistries extends ListRecords
{
    protected static string $resource = CrmDocumentRegistryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
