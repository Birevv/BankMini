<?php

namespace App\Filament\Teller\Pages;

use App\Actions\Transactions\CreateWithdrawalAction;
use App\Enums\UserRole;
use App\Models\Nasabah;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class Penarikan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi Loket';

    protected static ?string $navigationLabel = 'Penarikan';

    protected static ?string $title = 'Penarikan Tunai';

    protected string $view = 'filament.teller.pages.penarikan';

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

        $this->form->fill(['customer_id' => $customerId, 'pin' => null]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Data Penarikan')
                    ->description('Saldo dan PIN selalu diverifikasi ulang di server setelah rekening dikunci.')
                    ->columns(2)
                    ->schema([
                        $this->customerSelect(),
                        TextInput::make('nominal')
                            ->label('Nominal Penarikan')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->required(),
                        TextInput::make('pin')
                            ->label('PIN Nasabah')
                            ->password()
                            ->revealable(false)
                            ->autocomplete('off')
                            ->required()
                            ->length(6)
                            ->rule('digits:6')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $rawState = $this->form->getRawState();
        $data = $this->form->getState();

        try {
            /** @var User $actor */
            $actor = auth()->user();
            $transaction = app(CreateWithdrawalAction::class)->execute(
                $actor,
                (int) $data['customer_id'],
                $data['nominal'],
                (string) ($rawState['pin'] ?? ''),
            );
        } catch (ValidationException $exception) {
            $this->form->fill([...$this->data, 'pin' => null]);
            throw $exception;
        } catch (RuntimeException $exception) {
            $this->form->fill([...$this->data, 'pin' => null]);
            Notification::make()->danger()->title('Penarikan ditolak')->body($exception->getMessage())->send();

            return;
        }

        $this->form->fill(['pin' => null]);
        Notification::make()->success()->title('Penarikan berhasil')->body('Jurnal telah dibuat dan seimbang.')->send();
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
