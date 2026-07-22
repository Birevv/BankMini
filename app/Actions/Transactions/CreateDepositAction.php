<?php

namespace App\Actions\Transactions;

use App\Actions\Accounting\GenerateJournalAction;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Exceptions\InactiveCustomerException;
use App\Exceptions\UnauthorizedTransactionException;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateDepositAction
{
    public function __construct(
        private readonly GenerateJournalAction $generateJournal,
        private readonly AuditLogService $auditLog,
    ) {}

    public function execute(
        User $actor,
        int $customerId,
        mixed $amount,
        bool $slipConfirmed,
        bool $cashConfirmed,
    ): Transaksi {
        $this->ensureActiveTeller($actor);
        $normalizedAmount = Money::fromInput($amount);

        if ((! $slipConfirmed) || (! $cashConfirmed)) {
            throw ValidationException::withMessages([
                'confirmation' => 'Slip dan kesesuaian uang fisik harus dikonfirmasi.',
            ]);
        }

        return DB::transaction(function () use ($actor, $customerId, $normalizedAmount): Transaksi {
            $customer = Nasabah::query()->lockForUpdate()->findOrFail($customerId);

            if (! $customer->is_active) {
                throw new InactiveCustomerException;
            }

            $transaction = Transaksi::query()->create([
                'id_nasabah' => $customer->getKey(),
                'id_user' => $actor->getKey(),
                'tanggal' => now(),
                'jenis_trans' => TransactionType::DEPOSIT,
                'nominal' => $normalizedAmount,
            ]);

            $this->generateJournal->execute($transaction);
            $this->auditLog->record('transaction.deposit.created', $transaction, $actor, [
                'amount' => $normalizedAmount,
                'customer_id' => $customer->getKey(),
            ]);

            return $transaction->load(['nasabah', 'teller', 'journals']);
        }, attempts: 3);
    }

    private function ensureActiveTeller(User $actor): void
    {
        if ((! $actor->is_active) || $actor->role !== UserRole::TELLER) {
            throw new UnauthorizedTransactionException;
        }
    }
}
