<?php

namespace App\Filament\Resources\WorkOrderClosureResource\Pages;

use App\Filament\Resources\WorkOrderClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkOrderClosure extends EditRecord
{
    protected static string $resource = WorkOrderClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
