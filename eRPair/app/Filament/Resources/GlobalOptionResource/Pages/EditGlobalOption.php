<?php

namespace App\Filament\Resources\GlobalOptionResource\Pages;

use App\Filament\Resources\GlobalOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGlobalOption extends EditRecord
{
    protected static string $resource = GlobalOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
