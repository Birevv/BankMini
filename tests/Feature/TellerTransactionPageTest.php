<?php

namespace Tests\Feature;

use App\Actions\Transactions\CreateDepositAction;
use App\Filament\Teller\Pages\Penarikan;
use App\Filament\Teller\Pages\Setoran;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\BalanceService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TellerTransactionPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('teller'));
    }

    public function testTellerCanCreateDepositFromFilamentPage(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();

        $component = Livewire::actingAs($teller)
            ->test(Setoran::class)
            ->fillForm([
                'customer_id' => $customer->getKey(),
                'nominal' => 50_000,
                'slip_confirmed' => true,
                'cash_confirmed' => true,
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $transaction = Transaksi::query()->sole();

        $component->assertRedirect(route('transactions.receipt', $transaction));
        $this->assertSame(50_000, app(BalanceService::class)->forCustomer($customer->getKey()));
        $this->assertCount(2, $transaction->journals);
    }

    public function testTellerCanCreateWithdrawalFromFilamentPage(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->getKey(), 50_000, true, true);

        $component = Livewire::actingAs($teller)
            ->test(Penarikan::class)
            ->fillForm([
                'customer_id' => $customer->getKey(),
                'nominal' => 20_000,
                'pin' => '123456',
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $withdrawal = Transaksi::query()->latest('id')->firstOrFail();

        $component->assertRedirect(route('transactions.receipt', $withdrawal));
        $this->assertSame(30_000, app(BalanceService::class)->forCustomer($customer->getKey()));
        $this->assertCount(2, $withdrawal->journals);
    }
}
