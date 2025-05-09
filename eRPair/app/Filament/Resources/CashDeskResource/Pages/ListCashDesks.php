<?php

namespace App\Filament\Resources\CashDeskResource\Pages;

use App\Filament\Resources\CashDeskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashDesks extends ListRecords
{
    protected static string $resource = CashDeskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
