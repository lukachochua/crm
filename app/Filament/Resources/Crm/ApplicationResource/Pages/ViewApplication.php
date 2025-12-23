<?php

namespace App\Filament\Resources\Crm\ApplicationResource\Pages;

use App\Filament\Resources\Crm\ApplicationResource\Pages\Concerns\ConvertsApplicationToOrder;
use App\Filament\Resources\Crm\ApplicationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    use ConvertsApplicationToOrder;

    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->convertToOrderAction(),
        ];
    }
}
