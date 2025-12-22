<?php

namespace App\Filament\Resources\Hr\FeedbackRequestResource\Pages;

use App\Filament\Resources\Hr\FeedbackRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackRequests extends ListRecords
{
    protected static string $resource = FeedbackRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
