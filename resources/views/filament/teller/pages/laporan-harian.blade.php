<x-filament-panels::page>
    <form class="space-y-6">
        {{ $this->form }}
        <div class="flex flex-wrap gap-3">
            <x-filament::button type="button" wire:click="saveDraft" color="gray" icon="heroicon-o-document">
                Simpan Draf
            </x-filament::button>
            <x-filament::button type="button" wire:click="submitReport" color="primary" icon="heroicon-o-paper-airplane">
                Ajukan ke Supervisor
            </x-filament::button>
        </div>
    </form>

    @if ($report)
        <x-filament::section>
            <x-slot name="heading">Ringkasan {{ $report->tanggal->format('d M Y') }}</x-slot>
            <x-slot name="description">Status: {{ $report->status->label() }}</x-slot>
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div><dt class="text-sm text-gray-500">Total Setoran</dt><dd class="font-semibold">{{ \App\Support\Money::format($report->total_setoran) }}</dd></div>
                <div><dt class="text-sm text-gray-500">Total Penarikan</dt><dd class="font-semibold">{{ \App\Support\Money::format($report->total_penarikan) }}</dd></div>
                <div><dt class="text-sm text-gray-500">Saldo Sistem</dt><dd class="font-semibold">{{ \App\Support\Money::format($report->saldo_akhir_sistem) }}</dd></div>
                <div><dt class="text-sm text-gray-500">Saldo Fisik</dt><dd class="font-semibold">{{ \App\Support\Money::format($report->saldo_fisik) }}</dd></div>
                <div><dt class="text-sm text-gray-500">Selisih</dt><dd class="font-semibold {{ $report->selisih === 0 ? 'text-success-600' : 'text-danger-600' }}">{{ \App\Support\Money::format($report->selisih) }}</dd></div>
            </dl>
            <div class="mt-6">
                <x-filament::button tag="a" href="{{ route('daily-reports.print', $report) }}" target="_blank" color="gray" icon="heroicon-o-printer">
                    Cetak Laporan
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
