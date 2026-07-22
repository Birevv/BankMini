<?php

namespace App\Filament\Admin\Resources\NasabahResource\Pages;

use App\Filament\Admin\Resources\NasabahResource;
use App\Services\AccountNumberService;
use Filament\Resources\Pages\CreateRecord;

class CreateNasabah extends CreateRecord
{
    protected static string $resource = NasabahResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['no_rekening'] = app(AccountNumberService::class)->generate();

        return $data;
    }
}
