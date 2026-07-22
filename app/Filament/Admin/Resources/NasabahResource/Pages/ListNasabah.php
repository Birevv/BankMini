<?php

namespace App\Filament\Admin\Resources\NasabahResource\Pages;

use App\Filament\Admin\Resources\NasabahResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNasabah extends ListRecords
{
    protected static string $resource = NasabahResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah Nasabah')];
    }
}
