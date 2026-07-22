<?php

namespace App\Filament\Supervisor\Resources\AuditLogResource\Pages;

use App\Filament\Supervisor\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;
}
