<?php

namespace App\Filament\Pages;

use DB;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class StoreSelection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static string $view = 'filament.pages.store-selection';
    protected static bool $shouldRegisterNavigation = true; // No mostrar en menú

    public ?string $store_id = null;

    public function mount(): void
    {
        $this->form->fill();
    }
    protected function getFormSchema(): array
    {
        

        $stores = DB::table('stores')
    ->join('store_user', 'stores.id', '=', 'store_user.store_id')
    ->where('store_user.user_id', auth()->user()->id)
    ->select(DB::raw('`stores`.`name`, `stores`.`id`'))
    ->get();

// $stores es una colección de Eloquent models (StdClass)

    
        ////LOS PUTOS MUERTOS DE LA PUTA QUE PARIO A MYSQL
        return [
            Select::make('store_id')
                ->label('Selecciona una tienda')
                ->options($stores)
                ->required(),
        ];
    }
    

    public function submit(): void
    {
        $data = $this->form->getState();

        session(['store_id' => $data['store_id']]);

        Notification::make()
            ->title('Tienda seleccionada correctamente')
            ->success()
            ->send();

        redirect()->intended(filament()->getUrl());
    }
}
