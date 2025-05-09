<?php

namespace App\Filament\Resources\ClosureResource\Pages;

use App\Filament\Resources\ClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClosure extends EditRecord
{
    protected static string $resource = ClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
