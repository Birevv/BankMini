<?php

namespace App\Filament\Shared\Tables;

use App\Enums\TransactionType;
use App\Support\Money;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No. Transaksi')
                    ->formatStateUsing(fn (int $state): string => 'TRX-'.str_pad((string) $state, 8, '0', STR_PAD_LEFT))
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('nasabah.no_rekening')
                    ->label('No. Rekening')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nasabah.nama_siswa')
                    ->label('Nasabah')
                    ->searchable(),
                TextColumn::make('teller.nama_petugas')
                    ->label('Teller')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_trans')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (TransactionType $state): string => $state->label())
                    ->color(fn (TransactionType $state): string => $state === TransactionType::DEPOSIT ? 'success' : 'warning'),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => Money::format($state))
                    ->alignEnd()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('jenis_trans')
                    ->label('Jenis transaksi')
                    ->options(TransactionType::options()),
                SelectFilter::make('teller')
                    ->relationship('teller', 'nama_petugas')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('nasabah')
                    ->relationship('nasabah', 'nama_siswa')
                    ->searchable()
                    ->preload(),
                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('tanggal', '>=', $date))
                        ->when($data['sampai'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('tanggal', '<=', $date))),
            ])
            ->recordActions([ViewAction::make()->label('Lihat')])
            ->defaultSort('tanggal', 'desc')
            ->persistFiltersInSession()
            ->deferLoading();
    }
}
