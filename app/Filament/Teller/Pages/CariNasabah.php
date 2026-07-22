<?php

namespace App\Filament\Teller\Pages;

use App\Enums\UserRole;
use App\Models\Nasabah;
use App\Models\User;
use App\Services\BalanceService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CariNasabah extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi Loket';

    protected static ?string $navigationLabel = 'Cari Nasabah';

    protected static ?string $title = 'Cari Nasabah';

    protected string $view = 'filament.teller.pages.cari-nasabah';

    public string $noRekening = '';

    /** @var array<string, mixed>|null */
    public ?array $customer = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_active && $user->role === UserRole::TELLER;
    }

    public function search(): void
    {
        $this->validate([
            'noRekening' => ['required', 'string', 'max:50'],
        ], [], ['noRekening' => 'nomor rekening']);

        $accountNumber = str($this->noRekening)->trim()->upper()->toString();
        $customer = Nasabah::query()
            ->where('no_rekening', $accountNumber)
            ->where('is_active', true)
            ->first();

        if (! $customer) {
            $this->customer = null;
            $this->addError('noRekening', 'Rekening aktif tidak ditemukan.');

            return;
        }

        $this->noRekening = $customer->no_rekening;
        $this->customer = [
            'id' => $customer->getKey(),
            'no_rekening' => $customer->no_rekening,
            'nis' => $customer->nis,
            'nama_siswa' => $customer->nama_siswa,
            'kelas' => $customer->kelas,
            'saldo' => app(BalanceService::class)->forCustomer((int) $customer->getKey()),
        ];

        Notification::make()->success()->title('Nasabah ditemukan')->send();
    }
}
