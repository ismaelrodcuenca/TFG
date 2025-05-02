<?php

namespace App\Filament\Resources\ItemWorkOrderResource\Pages;

use App\Filament\Resources\ItemWorkOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemWorkOrder extends EditRecord
{
    protected static string $resource = ItemWorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
