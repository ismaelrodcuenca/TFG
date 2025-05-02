<?php

namespace App\Filament\Resources\DeviceModelItemResource\Pages;

use App\Filament\Resources\DeviceModelItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeviceModelItem extends EditRecord
{
    protected static string $resource = DeviceModelItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
