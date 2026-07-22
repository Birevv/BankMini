<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\JournalPosition;
use App\Enums\TransactionType;
use App\Models\Transaksi;
use App\Support\Money;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaksi')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('No. Transaksi')
                            ->formatStateUsing(fn (int $state): string => 'TRX-'.str_pad((string) $state, 8, '0', STR_PAD_LEFT)),
                        TextEntry::make('tanggal')->label('Tanggal')->dateTime('d M Y H:i:s'),
                        TextEntry::make('jenis_trans')
                            ->label('Jenis')
                            ->badge()
                            ->formatStateUsing(fn (TransactionType $state): string => $state->label()),
                        TextEntry::make('nasabah.no_rekening')->label('No. Rekening'),
                        TextEntry::make('nasabah.nama_siswa')->label('Nasabah'),
                        TextEntry::make('teller.nama_petugas')->label('Teller'),
                        TextEntry::make('nominal')
                            ->label('Nominal')
                            ->formatStateUsing(fn (int $state): string => Money::format($state)),
                        TextEntry::make('journal_balance')
                            ->label('Status jurnal')
                            ->state(function (Transaksi $record): string {
                                $debit = $record->journals->where('posisi', JournalPosition::DEBIT)->sum('jumlah');
                                $credit = $record->journals->where('posisi', JournalPosition::CREDIT)->sum('jumlah');

                                return $record->journals->count() === 2 && $debit === $credit ? 'Seimbang' : 'Tidak seimbang';
                            })
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Seimbang' ? 'success' : 'danger'),
                    ]),
                Section::make('Jurnal Double-Entry')
                    ->schema([
                        RepeatableEntry::make('journals')
                            ->label('')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('kode_akun')->label('Kode Akun'),
                                TextEntry::make('posisi')
                                    ->label('Posisi')
                                    ->badge()
                                    ->formatStateUsing(fn (JournalPosition $state): string => $state->label()),
                                TextEntry::make('jumlah')
                                    ->label('Jumlah')
                                    ->formatStateUsing(fn (int $state): string => Money::format($state)),
                            ]),
                    ]),
            ]);
    }
}
