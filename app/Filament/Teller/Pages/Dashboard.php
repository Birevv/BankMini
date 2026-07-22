<?php

namespace App\Filament\Teller\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Teller';

    public function getSubheading(): string|Htmlable|null
    {
        return 'Proses transaksi loket dan pantau posisi kas hari ini.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('searchCustomer')
                ->label('Cari nasabah')
                ->icon('heroicon-o-magnifying-glass')
                ->color('gray')
                ->url(CariNasabah::getUrl(panel: 'teller')),
            Action::make('deposit')
                ->label('Setoran')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(Setoran::getUrl(panel: 'teller')),
            Action::make('withdrawal')
                ->label('Penarikan')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->url(Penarikan::getUrl(panel: 'teller')),
        ];
    }
}
