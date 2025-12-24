<?php

namespace App\Filament\Resources\Crm\InternalTransferResource\Pages;

use App\Filament\Resources\Crm\InternalTransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInternalTransfer extends CreateRecord
{
    protected static string $resource = InternalTransferResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('view', ['record' => $this->record]);
    }
}
