<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-4">
        {{ $this->form }}
        <x-filament::button type="submit">
            Entrar
        </x-filament::button>
    </form>
</x-filament-panels::page>
