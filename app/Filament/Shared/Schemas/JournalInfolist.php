<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\JournalPosition;
use App\Support\Money;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Jurnal Akuntansi')
                ->columns(2)
                ->schema([
                    TextEntry::make('transaction.id')
                        ->label('No. Transaksi')
                        ->formatStateUsing(fn (int $state): string => 'TRX-'.str_pad((string) $state, 8, '0', STR_PAD_LEFT)),
                    TextEntry::make('transaction.tanggal')->label('Tanggal')->dateTime('d M Y H:i:s'),
                    TextEntry::make('kode_akun')->label('Kode Akun'),
                    TextEntry::make('posisi')
                        ->label('Posisi')
                        ->badge()
                        ->formatStateUsing(fn (JournalPosition $state): string => $state->label()),
                    TextEntry::make('jumlah')
                        ->label('Jumlah')
                        ->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('transaction.nasabah.nama_siswa')->label('Nasabah'),
                    TextEntry::make('transaction.teller.nama_petugas')->label('Teller'),
                ]),
        ]);
    }
}
