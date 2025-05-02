<?php

namespace App\Filament\Resources\StoreUserResource\Pages;

use App\Filament\Resources\StoreUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreUsers extends ListRecords
{
    protected static string $resource = StoreUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
