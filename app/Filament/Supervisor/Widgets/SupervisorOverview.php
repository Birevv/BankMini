<?php

namespace App\Filament\Supervisor\Widgets;

use App\Services\DashboardMetricsService;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupervisorOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan pengawasan';

    protected ?string $description = 'Transaksi dan laporan kas yang memerlukan perhatian.';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $metrics = app(DashboardMetricsService::class)->supervisor();

        return [
            Stat::make('Transaksi hari ini', number_format($metrics['transactions_today'], 0, ',', '.'))
                ->description('Seluruh transaksi loket')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->icon('heroicon-o-receipt-percent'),
            Stat::make('Perputaran hari ini', Money::format($metrics['turnover_today']))
                ->description('Akumulasi nominal transaksi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('Menunggu pemeriksaan', number_format($metrics['pending_reports'], 0, ',', '.'))
                ->description('Laporan telah diajukan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-o-document-magnifying-glass'),
            Stat::make('Disetujui hari ini', number_format($metrics['approved_today'], 0, ',', '.'))
                ->description('Laporan selesai diperiksa')
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
