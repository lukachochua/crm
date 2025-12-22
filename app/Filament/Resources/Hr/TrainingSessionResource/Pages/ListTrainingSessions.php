<?php

namespace App\Filament\Resources\Hr\TrainingSessionResource\Pages;

use App\Filament\Resources\Hr\TrainingSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingSessions extends ListRecords
{
    protected static string $resource = TrainingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
