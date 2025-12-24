<?php

namespace App\Filament\Resources\Crm\CustomerReturnResource\Pages;

use App\Filament\Resources\Crm\CustomerReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerReturns extends ListRecords
{
    protected static string $resource = CustomerReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
