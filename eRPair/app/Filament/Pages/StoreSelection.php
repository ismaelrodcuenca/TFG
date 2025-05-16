<?php
namespace App\Filament\Pages;

use App\Http\Controllers\StoreController;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class StoreSelection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static string $view = 'filament.pages.store-selection';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = "";
    protected ?string $heading = 'Seleccionar Tienda';
    protected static ?string $routePath = 'store-selection';

    public ?string $store_id = null;
    public ?string $rol_id = null;
    

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getHeading(): string
    {
        $this->userName = auth()->user()->name;
        return "Bienvenido, " . $this->userName;
    }


    protected function getFormSchema(): array
    {
        $stores = auth()->user()->stores()->pluck('stores.name', 'stores.id')->toArray();
        $roles = auth()->user()->roles()->pluck('roles.name', 'roles.id')->toArray();
        return [
            Select::make('store_id')
                ->label('Tienda')
                ->placeholder('Selecciones una tienda')
                ->options($stores)
                ->default(array_key_first($stores) ?? null)
                ->required(),
            Select::make('rol_id')
                ->placeholder('Selecciones un rol')
                ->label('Rol')
                ->options($roles)
                ->default(array_key_first($stores) ?? null)
                ->required(),
        ];
    }

    public function submit(): void
    {
        $storeId = $this->form->getState()['store_id'];
        $rolId = $this->form->getState()['rol_id'];
        session(['store_id' => $storeId]);
        session(['rol_id' => $rolId]);

        if (!session('store_id') || !session('rol_id')) {
            $notificationString = 'Seleccion requerida';
        }
        $notificationString = 'Bienvenido, '.auth()->user()->name;
        Notification::make()
            ->title('Tienda y perfil seleccionada correctamente')
            ->success()
            ->send();

        $this->redirect('/dashboard');
    }
}