<?php

namespace Tests\Feature;

use App\Actions\Transactions\CreateDepositAction;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NasabahPortalIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function testCustomerSeesOnlyOwnBalanceAndTransactions(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create(['nama_siswa' => 'Nasabah Pertama']);
        $otherCustomer = Nasabah::factory()->create(['nama_siswa' => 'Nasabah Rahasia']);
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);
        app(CreateDepositAction::class)->execute($teller, $otherCustomer->id, 900_000, true, true);

        $response = $this->actingAs($customer, 'nasabah')->get('/nasabah');

        $response->assertOk()
            ->assertSee('Nasabah Pertama')
            ->assertSee('Rp50.000')
            ->assertDontSee('Nasabah Rahasia')
            ->assertDontSee('Rp900.000');
    }

    public function testCustomerCannotAccessAnotherCustomersInternalTransactionUrl(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        $otherCustomer = Nasabah::factory()->create();
        $transaction = app(CreateDepositAction::class)->execute($teller, $otherCustomer->id, 50_000, true, true);

        $this->actingAs($customer, 'nasabah')
            ->get(route('transactions.receipt', $transaction))
            ->assertRedirect(route('login'));
    }

    public function testPortalLoginUsesSeparatePasswordAndRejectsWithdrawalPin(): void
    {
        $customer = Nasabah::factory()->create();

        $this->post(route('nasabah.login.store'), [
            'no_rekening' => $customer->no_rekening,
            'password' => '123456',
        ])->assertSessionHasErrors('no_rekening');

        $this->post(route('nasabah.login.store'), [
            'no_rekening' => $customer->no_rekening,
            'password' => 'nasabah123',
        ])->assertRedirect(route('nasabah.dashboard'));
    }

    public function testHomepagePortalButtonReturnsAuthenticatedCustomerToDashboard(): void
    {
        $customer = Nasabah::factory()->create();

        $this->actingAs($customer, 'nasabah')
            ->get(route('nasabah.login'))
            ->assertRedirect(route('nasabah.dashboard'));
    }
}
