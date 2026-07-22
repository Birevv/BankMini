<?php

namespace App\Filament\Admin\Resources\NasabahResource\Pages;

use App\Filament\Admin\Resources\NasabahResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNasabah extends EditRecord
{
    protected static string $resource = NasabahResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Hapus')];
    }
}
