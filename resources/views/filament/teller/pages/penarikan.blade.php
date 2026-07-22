<x-filament-panels::page>
    <x-filament::section compact>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            Cocokkan identitas nasabah dan nominal sebelum meminta PIN. Jangan mencatat, menyebutkan, atau menyimpan PIN nasabah.
        </p>
    </x-filament::section>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" color="warning" icon="heroicon-o-shield-check" wire:loading.attr="disabled" wire:target="submit">
            <span wire:loading.remove wire:target="submit">Verifikasi dan Proses Penarikan</span>
            <span wire:loading wire:target="submit">Memverifikasi Penarikan…</span>
        </x-filament::button>
    </form>
</x-filament-panels::page>
