<?php

namespace App\Filament\Resources\Hr\FeedbackCycleResource\Pages;

use App\Filament\Resources\Hr\FeedbackCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackCycles extends ListRecords
{
    protected static string $resource = FeedbackCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
