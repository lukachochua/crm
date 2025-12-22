<?php

namespace App\Filament\Resources\Hr\OnboardingTemplateResource\Pages;

use App\Filament\Resources\Hr\OnboardingTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingTemplates extends ListRecords
{
    protected static string $resource = OnboardingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
