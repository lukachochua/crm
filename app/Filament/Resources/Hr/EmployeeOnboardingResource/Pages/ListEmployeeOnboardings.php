<?php

namespace App\Filament\Resources\Hr\EmployeeOnboardingResource\Pages;

use App\Filament\Resources\Hr\EmployeeOnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeOnboardings extends ListRecords
{
    protected static string $resource = EmployeeOnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
