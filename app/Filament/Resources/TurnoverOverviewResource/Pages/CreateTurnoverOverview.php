<?php

namespace App\Filament\Resources\TurnoverOverviewResource\Pages;

use App\Filament\Resources\TurnoverOverviewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTurnoverOverview extends CreateRecord
{
    protected static string $resource = TurnoverOverviewResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
            return $resource::getUrl('edit', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
        }

        return $resource::getUrl('index');
    }
}
