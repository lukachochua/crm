<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource\Pages\Concerns\ConvertsApplicationToOrder;
use App\Filament\Resources\ApplicationResource;
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
