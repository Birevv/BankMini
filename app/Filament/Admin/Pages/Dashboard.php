<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\NasabahResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Administrator';

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pantau operasional sistem tanpa mengubah ledger transaksi.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createCustomer')
                ->label('Tambah nasabah')
                ->icon('heroicon-o-user-plus')
                ->url(NasabahResource::getUrl('create')),
        ];
    }
}
