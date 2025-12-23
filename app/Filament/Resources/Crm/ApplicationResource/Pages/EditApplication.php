<?php

namespace App\Filament\Resources\Crm\ApplicationResource\Pages;

use App\Filament\Resources\Crm\ApplicationResource\Pages\Concerns\ConvertsApplicationToOrder;
use App\Filament\Resources\Crm\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    use ConvertsApplicationToOrder;

    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->convertToOrderAction(),
            Actions\DeleteAction::make(),
        ];
    }
}
