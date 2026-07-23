<?php

namespace App\Filament\Admin\Resources\AuditLogResource\Pages;

use App\Filament\Admin\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    public function getSubheading(): ?string
    {
        return 'Riwayat aktivitas pengguna dan perubahan penting di dalam sistem.';
    }
}
