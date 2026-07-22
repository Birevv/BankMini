<?php

namespace App\Filament\Supervisor\Pages;

use App\Filament\Supervisor\Resources\LaporanHarianResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Supervisor';

    public function getSubheading(): string|Htmlable|null
    {
        return 'Tinjau transaksi, jurnal, audit, dan laporan kas Teller.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reviewReports')
                ->label('Periksa laporan')
                ->icon('heroicon-o-document-magnifying-glass')
                ->url(LaporanHarianResource::getUrl()),
        ];
    }
}
