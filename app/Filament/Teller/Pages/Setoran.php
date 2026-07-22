<?php

namespace App\Filament\Teller\Pages;

use App\Actions\Transactions\CreateDepositAction;
use App\Enums\UserRole;
use App\Models\Nasabah;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class Setoran extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi Loket';

    protected static ?string $navigationLabel = 'Setoran';

    protected static ?string $title = 'Setoran Tunai';

    protected string $view = 'filament.teller.pages.setoran';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_active && $user->role === UserRole::TELLER;
    }

    public function mount(): void
    {
        $customerId = Nasabah::query()
            ->where('no_rekening', request()->string('rekening')->upper()->toString())
            ->where('is_active', true)
            ->value('id');

        $this->form->fill(['customer_id' => $customerId]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Data Setoran')
                    ->columns(2)
                    ->schema([
                        $this->customerSelect(),
                        TextInput::make('nominal')
                            ->label('Nominal Setoran')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->required(),
                        Checkbox::make('slip_confirmed')
                            ->label('Slip setoran telah diperiksa dan lengkap')
                            ->accepted()
                            ->required(),
                        Checkbox::make('cash_confirmed')
                            ->label('Uang fisik telah dihitung dan sesuai')
                            ->accepted()
                            ->required(),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        try {
            /** @var User $actor */
            $actor = auth()->user();
            $transaction = app(CreateDepositAction::class)->execute(
                $actor,
                (int) $data['customer_id'],
                $data['nominal'],
                (bool) $data['slip_confirmed'],
                (bool) $data['cash_confirmed'],
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title('Setoran ditolak')->body($exception->getMessage())->send();

            return;
        }

        Notification::make()->success()->title('Setoran berhasil')->body('Jurnal telah dibuat dan seimbang.')->send();
        $this->redirect(route('transactions.receipt', $transaction));
    }

    private function customerSelect(): Select
    {
        return Select::make('customer_id')
            ->label('Nasabah')
            ->searchable()
            ->required()
            ->getSearchResultsUsing(fn (string $search): array => Nasabah::query()
                ->where('is_active', true)
                ->where(function ($query) use ($search): void {
                    $query->where('no_rekening', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                ->limit(25)
                ->get()
                ->mapWithKeys(fn (Nasabah $customer): array => [$customer->getKey() => "{$customer->no_rekening} · {$customer->nama_siswa}"])
                ->all())
            ->getOptionLabelUsing(function (mixed $value): ?string {
                $customer = Nasabah::query()->find($value);

                return $customer ? "{$customer->no_rekening} · {$customer->nama_siswa}" : null;
            });
    }
}
