<?php

namespace Tests\Feature;

use App\Actions\Transactions\CreateDepositAction;
use App\Actions\Transactions\CreateWithdrawalAction;
use App\Enums\JournalPosition;
use App\Enums\TransactionType;
use App\Exceptions\InvalidPinException;
use App\Exceptions\MinimumBalanceViolationException;
use App\Exceptions\UnauthorizedTransactionException;
use App\Models\Nasabah;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransactionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function testValidDepositCreatesBalancedDoubleEntryJournal(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();

        $transaction = app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);

        $this->assertSame(TransactionType::DEPOSIT, $transaction->jenis_trans);
        $this->assertSame(50_000, app(BalanceService::class)->forCustomer($customer->id));
        $this->assertCount(2, $transaction->journals);
        $this->assertSame(50_000, $transaction->journals->where('posisi', JournalPosition::DEBIT)->sum('jumlah'));
        $this->assertSame(50_000, $transaction->journals->where('posisi', JournalPosition::CREDIT)->sum('jumlah'));
    }

    public function testZeroAndNegativeDepositAreRejected(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();

        foreach ([0, -10_000] as $amount) {
            try {
                app(CreateDepositAction::class)->execute($teller, $customer->id, $amount, true, true);
                $this->fail('Nominal tidak valid seharusnya ditolak.');
            } catch (ValidationException) {
                $this->assertDatabaseCount('transaksi', 0);
            }
        }
    }

    public function testAdminAndSupervisorCannotCreateCounterTransactions(): void
    {
        $customer = Nasabah::factory()->create();

        foreach ([User::factory()->admin()->create(), User::factory()->supervisor()->create()] as $actor) {
            try {
                app(CreateDepositAction::class)->execute($actor, $customer->id, 50_000, true, true);
                $this->fail('Selain Teller tidak boleh membuat transaksi.');
            } catch (UnauthorizedTransactionException) {
                $this->assertDatabaseCount('transaksi', 0);
            }
        }
    }

    public function testValidWithdrawalMaintainsMinimumBalanceAndBalancedJournal(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);

        $withdrawal = app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 20_000, '123456');

        $this->assertSame(TransactionType::WITHDRAWAL, $withdrawal->jenis_trans);
        $this->assertSame(30_000, app(BalanceService::class)->forCustomer($customer->id));
        $this->assertCount(2, $withdrawal->journals);
        $this->assertSame(
            $withdrawal->journals->where('posisi', JournalPosition::DEBIT)->sum('jumlah'),
            $withdrawal->journals->where('posisi', JournalPosition::CREDIT)->sum('jumlah'),
        );
    }

    public function testInvalidPinRollsBackTransactionAndJournal(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);

        try {
            app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 10_000, '654321');
            $this->fail('PIN salah seharusnya ditolak.');
        } catch (InvalidPinException $exception) {
            $this->assertStringNotContainsString('654321', $exception->getMessage());
            $this->assertDatabaseCount('transaksi', 1);
            $this->assertDatabaseCount('jurnal_akuntansi', 2);
        }
    }

    public function testWithdrawalBelowMinimumBalanceIsRolledBack(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);

        $this->expectException(MinimumBalanceViolationException::class);

        try {
            app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 45_000, '123456');
        } finally {
            $this->assertDatabaseCount('transaksi', 1);
            $this->assertDatabaseCount('jurnal_akuntansi', 2);
            $this->assertSame(50_000, app(BalanceService::class)->forCustomer($customer->id));
        }
    }

    public function testSerializedWithdrawalsCannotOverdrawAccount(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 30_000, true, true);
        app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 10_000, '123456');

        try {
            app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 11_000, '123456');
            $this->fail('Penarikan kedua seharusnya ditolak setelah saldo dihitung ulang.');
        } catch (MinimumBalanceViolationException) {
            $this->assertSame(20_000, app(BalanceService::class)->forCustomer($customer->id));
            $this->assertDatabaseCount('transaksi', 2);
        }
    }
}
