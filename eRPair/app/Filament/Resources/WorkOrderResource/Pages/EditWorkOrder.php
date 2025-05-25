<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\WorkOrderController;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;
protected function getSaveFormAction(): \Filament\Actions\Action
{
    return parent::getSaveFormAction()
        ->disabled(function ($record): bool {
            if(PermissionHelper::NotAvailableOutsideStore($record)){
                return true;
            }
            return $record->created_at ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7 : true;
        });
}
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Hoja Pedido')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn($record) => route('generateWorkOrder', ['id' => $record->id]))
                ->openUrlInNewTab(true),
            Action::make("Cobrar")
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->disabled(function($record) { return PermissionHelper::NotAvailableOutsideStore($record);})
                ->openUrlInNewTab(true),
            Action::make('Devolución')
                ->icon('heroicon-o-credit-card')
                ->color(\Filament\Support\Colors\Color::Orange)
                ->disabled(function($record) { return PermissionHelper::NotAvailableOutsideStore($record);})
                ->openUrlInNewTab(),
            Action::make('Cancelar')
                ->icon('heroicon-o-credit-card')
                ->color(\Filament\Support\Colors\Color::Red)
                ->disabled(function($record) { return PermissionHelper::NotAvailableOutsideStore($record);})
                ->openUrlInNewTab(),
        ];
    }
}
