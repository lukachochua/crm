<?php

namespace App\Filament\Resources\Hr\SurveySubmissionResource\Pages;

use App\Filament\Resources\Hr\SurveySubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSurveySubmissions extends ListRecords
{
    protected static string $resource = SurveySubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
