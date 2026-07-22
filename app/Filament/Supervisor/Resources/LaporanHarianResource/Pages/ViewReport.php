<?php

namespace App\Filament\Supervisor\Resources\LaporanHarianResource\Pages;

use App\Filament\Supervisor\Resources\LaporanHarianResource;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = LaporanHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LaporanHarianResource::approveAction(),
            LaporanHarianResource::rejectAction(),
        ];
    }
}
