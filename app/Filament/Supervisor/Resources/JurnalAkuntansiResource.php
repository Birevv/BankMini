<?php

namespace App\Filament\Supervisor\Resources;

use App\Filament\Shared\Schemas\JournalInfolist;
use App\Filament\Shared\Tables\JournalTable;
use App\Filament\Supervisor\Resources\JurnalAkuntansiResource\Pages;
use App\Models\JurnalAkuntansi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JurnalAkuntansiResource extends Resource
{
    protected static ?string $model = JurnalAkuntansi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Pengawasan';

    protected static ?string $modelLabel = 'Jurnal Akuntansi';

    protected static ?string $pluralModelLabel = 'Jurnal Akuntansi';

    protected static ?string $slug = 'jurnal-akuntansi';

    public static function infolist(Schema $schema): Schema
    {
        return JournalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournals::route('/'),
            'view' => Pages\ViewJournal::route('/{record}'),
        ];
    }
}
