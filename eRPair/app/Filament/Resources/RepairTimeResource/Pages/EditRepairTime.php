<?php

namespace App\Filament\Resources\RepairTimeResource\Pages;

use App\Filament\Resources\RepairTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepairTime extends EditRecord
{
    protected static string $resource = RepairTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
