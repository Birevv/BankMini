<?php

namespace App\Filament\Teller\Pages;

use App\Actions\DailyReports\CloseDailyCashAction;
use App\Actions\DailyReports\SubmitDailyReportAction;
use App\Enums\UserRole;
use App\Models\LaporanHarianTeller;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use RuntimeException;

class LaporanHarian extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|\UnitEnum|null $navigationGroup = 'Penutupan Kas';

    protected static ?string $navigationLabel = 'Laporan Harian';

    protected static ?string $title = 'Laporan Penutupan Kas Harian';

    protected string $view = 'filament.teller.pages.laporan-harian';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public ?LaporanHarianTeller $report = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_active && $user->role === UserRole::TELLER;
    }

    public function mount(): void
    {
        $this->form->fill([
            'tanggal' => today()->toDateString(),
            'saldo_awal' => 0,
        ]);
        $this->loadReport(today()->toDateString());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Rekonsiliasi Kas')
                    ->description('Total transaksi dihitung ulang dari ledger oleh sistem.')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Operasional')
                            ->required()
                            ->maxDate(today()),
                        TextInput::make('saldo_awal')
                            ->label('Saldo Awal')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('saldo_fisik')
                            ->label('Saldo Fisik')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Textarea::make('catatan_teller')
                            ->label('Catatan Teller')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function saveDraft(): void
    {
        $this->report = $this->closeReport();
        Notification::make()->success()->title('Draf laporan tersimpan')->send();
    }

    public function submitReport(): void
    {
        try {
            $report = $this->closeReport();
            /** @var User $actor */
            $actor = auth()->user();
            $this->report = app(SubmitDailyReportAction::class)->execute($report, $actor);
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title('Laporan belum dapat diajukan')->body($exception->getMessage())->send();

            return;
        }

        Notification::make()->success()->title('Laporan diajukan ke Supervisor')->send();
    }

    private function closeReport(): LaporanHarianTeller
    {
        $data = $this->form->getState();
        /** @var User $actor */
        $actor = auth()->user();

        return app(CloseDailyCashAction::class)->execute(
            $actor,
            Carbon::parse($data['tanggal']),
            $data['saldo_awal'],
            $data['saldo_fisik'],
            $data['catatan_teller'] ?? null,
        );
    }

    private function loadReport(string $date): void
    {
        $this->report = LaporanHarianTeller::query()
            ->where('id_teller', auth()->id())
            ->whereDate('tanggal', $date)
            ->first();
    }
}
