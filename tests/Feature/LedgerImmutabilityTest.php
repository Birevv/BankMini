<?php

namespace Tests\Feature;

use App\Actions\Transactions\CreateDepositAction;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class LedgerImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    public function testTransactionCannotBeEditedOrDeleted(): void
    {
        $transaction = app(CreateDepositAction::class)->execute(
            User::factory()->teller()->create(),
            Nasabah::factory()->create()->id,
            50_000,
            true,
            true,
        );

        try {
            $transaction->update(['nominal' => 75_000]);
            $this->fail('Ledger transaksi seharusnya immutable.');
        } catch (LogicException) {
            $this->assertDatabaseHas('transaksi', ['id' => $transaction->id, 'nominal' => 50_000]);
        }

        $this->expectException(LogicException::class);
        $transaction->delete();
    }

    public function testJournalCannotBeEditedOrDeleted(): void
    {
        $transaction = app(CreateDepositAction::class)->execute(
            User::factory()->teller()->create(),
            Nasabah::factory()->create()->id,
            50_000,
            true,
            true,
        );
        $journal = $transaction->journals->firstOrFail();

        try {
            $journal->update(['jumlah' => 1]);
            $this->fail('Jurnal seharusnya immutable.');
        } catch (LogicException) {
            $this->assertDatabaseHas('jurnal_akuntansi', ['id' => $journal->id, 'jumlah' => 50_000]);
        }

        $this->expectException(LogicException::class);
        $journal->delete();
    }
}
