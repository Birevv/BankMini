<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NasabahResource\Pages;
use App\Models\Nasabah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NasabahResource extends Resource
{
    protected static ?string $model = Nasabah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Nasabah';

    protected static ?string $pluralModelLabel = 'Nasabah';

    protected static ?string $recordTitleAttribute = 'nama_siswa';

    protected static ?string $slug = 'nasabah';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil Nasabah')
                ->columns(2)
                ->schema([
                    TextInput::make('no_rekening')
                        ->label('Nomor Rekening')
                        ->placeholder('Dibuat otomatis saat data disimpan')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('nis')
                        ->label('NIS')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    TextInput::make('nama_siswa')
                        ->label('Nama Siswa')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('kelas')
                        ->label('Kelas')
                        ->required()
                        ->maxLength(100),
                    Toggle::make('is_active')
                        ->label('Rekening aktif')
                        ->default(true)
                        ->required(),
                ]),
            Section::make('Kredensial Aman')
                ->description('PIN hanya untuk otorisasi penarikan. Kata sandi portal digunakan untuk masuk ke portal nasabah.')
                ->columns(2)
                ->schema([
                    TextInput::make('pin_keamanan')
                        ->label('PIN 6 Digit')
                        ->password()
                        ->revealable(false)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->length(6)
                        ->rule('digits:6')
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    TextInput::make('portal_password')
                        ->label('Kata Sandi Portal')
                        ->password()
                        ->revealable(false)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rekening')->label('No. Rekening')->searchable()->sortable(),
                TextColumn::make('nis')->label('NIS')->searchable()->sortable(),
                TextColumn::make('nama_siswa')->label('Nama Siswa')->searchable()->sortable(),
                TextColumn::make('kelas')->label('Kelas')->searchable()->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean()->sortable(),
                TextColumn::make('transactions_count')->label('Transaksi')->counts('transactions')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Status aktif'),
            ])
            ->recordActions([
                Action::make('qr')
                    ->label('QR Code')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->url(fn (Nasabah $record): string => route('admin.nasabah.qr', $record))
                    ->openUrlInNewTab(),
                Action::make('resetPin')
                    ->label('Reset PIN')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->schema([
                        TextInput::make('pin')
                            ->label('PIN Baru')
                            ->password()
                            ->revealable(false)
                            ->required()
                            ->length(6)
                            ->rule('digits:6'),
                    ])
                    ->action(function (Nasabah $record, array $data): void {
                        $record->update(['pin_keamanan' => $data['pin']]);
                        Notification::make()->success()->title('PIN berhasil direset')->send();
                    }),
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->defaultSort('nama_siswa');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNasabah::route('/'),
            'create' => Pages\CreateNasabah::route('/create'),
            'edit' => Pages\EditNasabah::route('/{record}/edit'),
        ];
    }
}
