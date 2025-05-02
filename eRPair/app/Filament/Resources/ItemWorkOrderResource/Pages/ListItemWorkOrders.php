<?php

namespace App\Filament\Resources\ItemWorkOrderResource\Pages;

use App\Filament\Resources\ItemWorkOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemWorkOrders extends ListRecords
{
    protected static string $resource = ItemWorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
