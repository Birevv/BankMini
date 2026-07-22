<x-filament-panels::page>
    <x-filament::section compact>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            Pastikan nama nasabah, slip, dan jumlah uang fisik sudah sesuai sebelum memproses transaksi. Transaksi yang berhasil tidak dapat diedit atau dihapus.
        </p>
    </x-filament::section>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" color="success" icon="heroicon-o-check-circle" wire:loading.attr="disabled" wire:target="submit">
            <span wire:loading.remove wire:target="submit">Proses Setoran</span>
            <span wire:loading wire:target="submit">Memproses Setoran…</span>
        </x-filament::button>
    </form>
</x-filament-panels::page>
