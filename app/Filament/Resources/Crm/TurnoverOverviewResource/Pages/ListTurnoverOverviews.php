<?php

namespace App\Filament\Resources\Crm\TurnoverOverviewResource\Pages;

use App\Filament\Resources\Crm\TurnoverOverviewResource;
use Filament\Resources\Pages\ListRecords;

class ListTurnoverOverviews extends ListRecords
{
    protected static string $resource = TurnoverOverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
