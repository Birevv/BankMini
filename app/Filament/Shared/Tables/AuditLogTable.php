<?php

namespace App\Filament\Shared\Tables;

use App\Models\AuditLog;
use App\Support\AuditLogPresentation;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AuditLogTable
{
    public static function configure(Table $table): Table
    {
        return TableToolbar::configure($table, 'aktivitas tercatat', 'audit-log-table')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['actor', 'subject']))
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('actor.nama_petugas')
                    ->label('Aktor')
                    ->state(fn (AuditLog $record): string => $record->actor?->nama_petugas ?? 'Sistem / Nasabah')
                    ->extraCellAttributes(fn (AuditLog $record): array => blank($record->actor?->nama_petugas)
                        ? ['class' => 'audit-log-system-actor']
                        : [])
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Aktivitas')
                    ->badge()
                    ->formatStateUsing(fn (AuditLog $record): string => AuditLogPresentation::activityLabel($record))
                    ->color(fn (string $state): string => AuditLogPresentation::activityColor($state))
                    ->tooltip(fn (AuditLog $record): string => $record->action)
                    ->searchable(),
                TextColumn::make('subject_type')->label('Jenis Subjek')->formatStateUsing(fn (?string $state): string => class_basename($state ?? '-')),
                TextColumn::make('subject_id')->label('ID Subjek'),
                TextColumn::make('ip_address')->label('Alamat IP'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Aktivitas')
                    ->options(fn (): array => AuditLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action')
                        ->mapWithKeys(fn (string $eventCode): array => [
                            $eventCode => AuditLogPresentation::activityLabelForCode($eventCode),
                        ])
                        ->all())
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label('Lihat detail')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('primary')
                    ->modalHeading(fn (AuditLog $record): string => AuditLogPresentation::activityLabel($record))
                    ->modalContent(fn (AuditLog $record): View => view('filament.shared.audit-log-detail', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modal()
                    ->slideOver(),
            ])
            ->recordActionsColumnLabel('Detail')
            ->searchPlaceholder('Cari aktivitas, aktor, atau kode event')
            ->defaultSort('created_at', 'desc')
            ->deferLoading();
    }
}
