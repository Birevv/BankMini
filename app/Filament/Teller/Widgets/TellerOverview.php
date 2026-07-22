<?php

namespace App\Filament\Teller\Widgets;

use App\Models\User;
use App\Services\DashboardMetricsService;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TellerOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Aktivitas loket hari ini';

    protected ?string $description = 'Ringkasan transaksi yang Anda proses.';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        /** @var User $teller */
        $teller = auth()->user();
        $metrics = app(DashboardMetricsService::class)->teller($teller);

        return [
            Stat::make('Total transaksi', number_format($metrics['transactions'], 0, ',', '.'))
                ->description('Transaksi loket hari ini')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->icon('heroicon-o-arrows-right-left'),
            Stat::make('Total setoran', Money::format($metrics['deposits']))
                ->description('Kas masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Total penarikan', Money::format($metrics['withdrawals']))
                ->description('Kas keluar hari ini')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('warning')
                ->icon('heroicon-o-wallet'),
            Stat::make('Posisi kas bersih', Money::format($metrics['net_cash']))
                ->description('Setoran dikurangi penarikan')
                ->descriptionIcon('heroicon-m-scale')
                ->icon('heroicon-o-calculator'),
        ];
    }
}
