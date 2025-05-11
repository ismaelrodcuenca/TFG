<!-- resources/views/filament/dashboard/pages/store-selection.blade.php -->
<x-filament::page class="">
    <x-filament::card class="">
        <!-- ME GUSTARIA QEU SE VIERA BIEN. A SER POSIBLE OCULTAR LA SIDEBAR-->
        <div>
            <h1 class="text-2xl font-bold mb-4">{{ $this->getTitle() }}</h1>
            {{ $this->form }}
        </div>
        <br>
        
        <div class="flex justify-center">
            <x-filament::button wire:click="submit" class="w-1/2 mt-4">
            Acceder
            </x-filament::button>
        </div>
    </x-filament::card>
</x-filament::page>

