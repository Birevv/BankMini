<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <x-filament::section class="lg:col-span-2">
            <x-slot name="heading">Nomor Rekening</x-slot>
            <x-slot name="description">Masukkan nomor rekening atau pindai QR Code kartu nasabah.</x-slot>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input
                        type="text"
                        wire:model="noRekening"
                        wire:keydown.enter="search"
                        placeholder="BM-2026-000001"
                        data-account-number-input
                    />
                </x-filament::input.wrapper>
                <x-filament::button wire:click="search" icon="heroicon-o-magnifying-glass">
                    Cari Nasabah
                </x-filament::button>
            </div>
            @error('noRekening')
                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
            @enderror
        </x-filament::section>

        <x-filament::section data-qr-scanner>
            <x-slot name="heading">Pemindai QR</x-slot>
            <video class="hidden aspect-square w-full rounded-lg bg-gray-950 object-cover" data-qr-video playsinline></video>
            <p class="mb-3 text-sm text-gray-500" data-qr-status>Kamera belum aktif.</p>
            <x-filament::button color="gray" class="w-full" data-qr-toggle icon="heroicon-o-qr-code">
                Aktifkan Kamera
            </x-filament::button>
        </x-filament::section>
    </div>

    @if ($customer)
        <x-filament::section>
            <x-slot name="heading">{{ $customer['nama_siswa'] }}</x-slot>
            <x-slot name="description">{{ $customer['no_rekening'] }}</x-slot>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-sm text-gray-500">NIS</dt><dd class="font-semibold">{{ $customer['nis'] }}</dd></div>
                <div><dt class="text-sm text-gray-500">Kelas</dt><dd class="font-semibold">{{ $customer['kelas'] }}</dd></div>
                <div><dt class="text-sm text-gray-500">Saldo</dt><dd class="font-semibold">{{ \App\Support\Money::format($customer['saldo']) }}</dd></div>
                <div class="flex gap-2">
                    <x-filament::button tag="a" href="{{ \App\Filament\Teller\Pages\Setoran::getUrl(['rekening' => $customer['no_rekening']], panel: 'teller') }}" color="success">Setoran</x-filament::button>
                    <x-filament::button tag="a" href="{{ \App\Filament\Teller\Pages\Penarikan::getUrl(['rekening' => $customer['no_rekening']], panel: 'teller') }}" color="warning">Penarikan</x-filament::button>
                </div>
            </dl>
        </x-filament::section>
    @endif

    <script src="{{ asset('js/qr-scanner.js') }}" defer></script>
</x-filament-panels::page>
