<?php

namespace App\Filament\Resources\WorkOrderClosureResource\Pages;

use App\Filament\Resources\WorkOrderClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkOrderClosures extends ListRecords
{
    protected static string $resource = WorkOrderClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
