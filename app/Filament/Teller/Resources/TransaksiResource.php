<?php

namespace App\Filament\Teller\Resources;

use App\Filament\Shared\Schemas\TransactionInfolist;
use App\Filament\Shared\Tables\TransactionTable;
use App\Filament\Teller\Resources\TransaksiResource\Pages;
use App\Models\Transaksi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Riwayat Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Riwayat Transaksi';

    protected static ?string $slug = 'riwayat-transaksi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id_user', auth()->id());
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksi::route('/'),
            'view' => Pages\ViewTransaksi::route('/{record}'),
        ];
    }
}
