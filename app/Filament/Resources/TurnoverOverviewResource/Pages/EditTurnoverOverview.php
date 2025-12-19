<?php

namespace App\Filament\Resources\TurnoverOverviewResource\Pages;

use App\Filament\Resources\TurnoverOverviewResource;
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
