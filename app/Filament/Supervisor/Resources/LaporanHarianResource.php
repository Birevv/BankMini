<?php

namespace App\Filament\Supervisor\Resources;

use App\Actions\DailyReports\ApproveDailyReportAction;
use App\Actions\DailyReports\RejectDailyReportAction;
use App\Enums\DailyReportStatus;
use App\Filament\Shared\Tables\TableToolbar;
use App\Filament\Supervisor\Resources\LaporanHarianResource\Pages;
use App\Models\LaporanHarianTeller;
use App\Models\User;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporanHarianResource extends Resource
{
    protected static ?string $model = LaporanHarianTeller::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Pengawasan';

    protected static ?string $modelLabel = 'Laporan Harian';

    protected static ?string $pluralModelLabel = 'Laporan Harian Teller';

    protected static ?string $slug = 'laporan-harian';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', [
                DailyReportStatus::SUBMITTED->value,
                DailyReportStatus::APPROVED->value,
                DailyReportStatus::REJECTED->value,
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan Penutupan Kas')
                ->columns(3)
                ->schema([
                    TextEntry::make('tanggal')->label('Tanggal')->date('d M Y'),
                    TextEntry::make('teller.nama_petugas')->label('Teller'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(fn (DailyReportStatus $state): string => $state->label()),
                    TextEntry::make('saldo_awal')->label('Saldo Awal')->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('total_setoran')->label('Total Setoran')->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('total_penarikan')->label('Total Penarikan')->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('saldo_akhir_sistem')->label('Saldo Sistem')->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('saldo_fisik')->label('Saldo Fisik')->formatStateUsing(fn (int $state): string => Money::format($state)),
                    TextEntry::make('selisih')
                        ->label('Selisih')
                        ->formatStateUsing(fn (int $state): string => Money::format($state))
                        ->badge()
                        ->color(fn (int $state): string => $state === 0 ? 'success' : 'danger'),
                    TextEntry::make('catatan_teller')->label('Catatan Teller')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('catatan_supervisor')->label('Catatan Supervisor')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('supervisor.nama_petugas')->label('Disetujui Oleh')->placeholder('-'),
                    TextEntry::make('approved_at')->label('Waktu Persetujuan')->dateTime('d M Y H:i')->placeholder('-'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TableToolbar::configure($table, 'laporan tersedia')
            ->columns([
                TextColumn::make('tanggal')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('teller.nama_petugas')->label('Teller')->searchable()->sortable(),
                TextColumn::make('total_setoran')->label('Setoran')->formatStateUsing(fn (int $state): string => Money::format($state))->alignEnd(),
                TextColumn::make('total_penarikan')->label('Penarikan')->formatStateUsing(fn (int $state): string => Money::format($state))->alignEnd(),
                TextColumn::make('saldo_akhir_sistem')->label('Saldo Sistem')->formatStateUsing(fn (int $state): string => Money::format($state))->alignEnd(),
                TextColumn::make('saldo_fisik')->label('Saldo Fisik')->formatStateUsing(fn (int $state): string => Money::format($state))->alignEnd(),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->formatStateUsing(fn (int $state): string => Money::format($state))
                    ->badge()
                    ->color(fn (int $state): string => $state === 0 ? 'success' : 'danger'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (DailyReportStatus $state): string => $state->label())
                    ->color(fn (DailyReportStatus $state): string => match ($state) {
                        DailyReportStatus::SUBMITTED => 'info',
                        DailyReportStatus::APPROVED => 'success',
                        DailyReportStatus::REJECTED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options(DailyReportStatus::options()),
                SelectFilter::make('teller')->relationship('teller', 'nama_petugas')->searchable()->preload(),
            ])
            ->recordActions([
                ViewAction::make()->label('Lihat'),
                self::approveAction(),
                self::rejectAction(),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Setujui laporan harian?')
            ->modalDescription('Laporan akan dikunci permanen setelah disetujui.')
            ->visible(fn (LaporanHarianTeller $record): bool => auth()->user()?->can('approve', $record) ?? false)
            ->action(function (LaporanHarianTeller $record): void {
                /** @var User $actor */
                $actor = auth()->user();
                app(ApproveDailyReportAction::class)->execute($record, $actor);
                Notification::make()->success()->title('Laporan berhasil disetujui')->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->visible(fn (LaporanHarianTeller $record): bool => auth()->user()?->can('reject', $record) ?? false)
            ->schema([
                Textarea::make('catatan_supervisor')
                    ->label('Alasan Penolakan')
                    ->required()
                    ->maxLength(1000),
            ])
            ->action(function (LaporanHarianTeller $record, array $data): void {
                /** @var User $actor */
                $actor = auth()->user();
                app(RejectDailyReportAction::class)->execute($record, $actor, $data['catatan_supervisor']);
                Notification::make()->success()->title('Laporan ditolak dan dikembalikan ke Teller')->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
