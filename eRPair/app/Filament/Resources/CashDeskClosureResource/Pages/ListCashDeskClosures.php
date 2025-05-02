<?php

namespace App\Filament\Resources\CashDeskClosureResource\Pages;

use App\Filament\Resources\CashDeskClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashDeskClosures extends ListRecords
{
    protected static string $resource = CashDeskClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
