<?php

namespace App\Filament\Supervisor\Resources\TransaksiResource\Pages;

use App\Filament\Supervisor\Resources\TransaksiResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTransaksi extends ViewRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('receipt')
                ->label('Cetak Struk')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn (): string => route('transactions.receipt', $this->getRecord()))
                ->openUrlInNewTab(),
        ];
    }
}
