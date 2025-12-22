<?php

namespace App\Filament\Resources\Hr\EngagementSurveyResource\Pages;

use App\Filament\Resources\Hr\EngagementSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEngagementSurveys extends ListRecords
{
    protected static string $resource = EngagementSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
