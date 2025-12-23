<?php

namespace App\Filament\Resources\Crm\TurnoverOverviewResource\Pages;

use App\Filament\Resources\Crm\TurnoverOverviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTurnoverOverview extends EditRecord
{
    protected static string $resource = TurnoverOverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
