<?php

namespace App\Filament\Resources\ClosureResource\Pages;

use App\Filament\Resources\ClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClosures extends ListRecords
{
    protected static string $resource = ClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
