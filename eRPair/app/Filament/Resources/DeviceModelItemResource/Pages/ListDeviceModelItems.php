<?php

namespace App\Filament\Resources\DeviceModelItemResource\Pages;

use App\Filament\Resources\DeviceModelItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeviceModelItems extends ListRecords
{
    protected static string $resource = DeviceModelItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
