<?php

namespace App\Filament\Resources\StoreUserResource\Pages;

use App\Filament\Resources\StoreUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreUser extends EditRecord
{
    protected static string $resource = StoreUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
