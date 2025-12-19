<?php

namespace App\Filament\Resources\TurnoverOverviewResource\Pages;

use App\Filament\Resources\TurnoverOverviewResource;
use Filament\Resources\Pages\ListRecords;

class ListTurnoverOverviews extends ListRecords
{
    protected static string $resource = TurnoverOverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
