<?php

namespace App\Filament\Shared\Tables;

use App\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('actor.nama_petugas')->label('Aktor')->placeholder('Sistem / Nasabah')->searchable(),
                TextColumn::make('action')->label('Aktivitas')->badge()->searchable(),
                TextColumn::make('subject_type')->label('Jenis Subjek')->formatStateUsing(fn (?string $state): string => class_basename($state ?? '-')),
                TextColumn::make('subject_id')->label('ID Subjek'),
                TextColumn::make('ip_address')->label('Alamat IP'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options(fn (): array => AuditLog::query()->distinct()->orderBy('action')->pluck('action', 'action')->all())
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading();
    }
}
