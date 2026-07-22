<?php

namespace App\Filament\Shared\Tables;

use App\Enums\JournalPosition;
use App\Enums\TransactionType;
use App\Models\JurnalAkuntansi;
use App\Support\Money;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JournalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction.id')
                    ->label('No. Transaksi')
                    ->formatStateUsing(fn (int $state): string => 'TRX-'.str_pad((string) $state, 8, '0', STR_PAD_LEFT))
                    ->sortable(),
                TextColumn::make('transaction.tanggal')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('transaction.nasabah.nama_siswa')->label('Nasabah')->searchable(),
                TextColumn::make('transaction.jenis_trans')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (TransactionType $state): string => $state->label()),
                TextColumn::make('kode_akun')->label('Kode Akun')->searchable()->sortable(),
                TextColumn::make('posisi')
                    ->label('Posisi')
                    ->badge()
                    ->formatStateUsing(fn (JournalPosition $state): string => $state->label())
                    ->color(fn (JournalPosition $state): string => $state === JournalPosition::DEBIT ? 'info' : 'warning'),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn (int $state): string => Money::format($state))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('balance_status')
                    ->label('Balance')
                    ->state(function (JurnalAkuntansi $record): string {
                        $debit = $record->transaction->journals->where('posisi', JournalPosition::DEBIT)->sum('jumlah');
                        $credit = $record->transaction->journals->where('posisi', JournalPosition::CREDIT)->sum('jumlah');

                        return $debit === $credit ? 'Seimbang' : 'Tidak seimbang';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Seimbang' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('kode_akun')
                    ->options([
                        (string) config('bank.accounts.cash') => '101 · Kas',
                        (string) config('bank.accounts.customer_savings') => '201 · Tabungan Nasabah',
                    ]),
                SelectFilter::make('posisi')->options(JournalPosition::options()),
                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $query, string $date): Builder => $query->whereHas(
                            'transaction',
                            fn (Builder $query): Builder => $query->whereDate('tanggal', '>=', $date),
                        ))
                        ->when($data['sampai'] ?? null, fn (Builder $query, string $date): Builder => $query->whereHas(
                            'transaction',
                            fn (Builder $query): Builder => $query->whereDate('tanggal', '<=', $date),
                        ))),
            ])
            ->recordActions([ViewAction::make()->label('Lihat')])
            ->defaultSort('id', 'desc')
            ->deferLoading();
    }
}
