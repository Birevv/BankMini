<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Shared\Tables\TableToolbar;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Petugas';

    protected static ?string $pluralModelLabel = 'Petugas Internal';

    protected static ?string $recordTitleAttribute = 'nama_petugas';

    protected static ?string $slug = 'petugas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Akun Petugas')
                ->columns(2)
                ->schema([
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->alphaDash()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    TextInput::make('nama_petugas')
                        ->label('Nama Petugas')
                        ->required()
                        ->maxLength(255),
                    Select::make('role')
                        ->label('Peran')
                        ->options(UserRole::options())
                        ->required()
                        ->native(false),
                    Toggle::make('is_active')
                        ->label('Akun aktif')
                        ->default(true)
                        ->required(),
                    TextInput::make('password')
                        ->label('Kata Sandi')
                        ->password()
                        ->revealable(false)
                        ->autocomplete('new-password')
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->maxLength(255)
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TableToolbar::configure($table, 'petugas terdaftar')
            ->columns([
                TextColumn::make('username')->label('Username')->searchable()->sortable(),
                TextColumn::make('nama_petugas')->label('Nama Petugas')->searchable()->sortable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state): string => $state->label()),
                IconColumn::make('is_active')->label('Aktif')->boolean()->sortable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Peran')->options(UserRole::options()),
                TernaryFilter::make('is_active')->label('Status aktif'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->defaultSort('nama_petugas');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
