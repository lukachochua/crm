<?php

namespace App\Filament\Resources\Crm\InternalTransferResource\Pages;

use App\Filament\Resources\Crm\InternalTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternalTransfers extends ListRecords
{
    protected static string $resource = InternalTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
