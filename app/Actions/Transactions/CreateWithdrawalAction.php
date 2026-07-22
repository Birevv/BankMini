<?php

namespace App\Actions\Transactions;

use App\Actions\Accounting\GenerateJournalAction;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Exceptions\InactiveCustomerException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidPinException;
use App\Exceptions\MinimumBalanceViolationException;
use App\Exceptions\UnauthorizedTransactionException;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\BalanceService;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateWithdrawalAction
{
    public function __construct(
        private readonly GenerateJournalAction $generateJournal,
        private readonly BalanceService $balanceService,
        private readonly AuditLogService $auditLog,
    ) {}

    public function execute(User $actor, int $customerId, mixed $amount, string $pin): Transaksi
    {
        $this->ensureActiveTeller($actor);
        $normalizedAmount = Money::fromInput($amount);

        if (! preg_match('/^\d{6}$/', $pin)) {
            throw ValidationException::withMessages([
                'pin' => 'PIN harus terdiri dari tepat enam digit.',
            ]);
        }

        return DB::transaction(function () use ($actor, $customerId, $normalizedAmount, $pin): Transaksi {
            $customer = Nasabah::query()->lockForUpdate()->findOrFail($customerId);

            if (! $customer->is_active) {
                throw new InactiveCustomerException;
            }

            $currentBalance = $this->balanceService->forCustomer((int) $customer->getKey());

            if (! Hash::check($pin, $customer->pin_keamanan)) {
                throw new InvalidPinException;
            }

            if ($normalizedAmount > $currentBalance) {
                throw new InsufficientBalanceException;
            }

            $minimumBalance = (int) config('bank.minimum_balance');

            if (($currentBalance - $normalizedAmount) < $minimumBalance) {
                throw new MinimumBalanceViolationException($minimumBalance);
            }

            $transaction = Transaksi::query()->create([
                'id_nasabah' => $customer->getKey(),
                'id_user' => $actor->getKey(),
                'tanggal' => now(),
                'jenis_trans' => TransactionType::WITHDRAWAL,
                'nominal' => $normalizedAmount,
            ]);

            $this->generateJournal->execute($transaction);
            $this->auditLog->record('transaction.withdrawal.created', $transaction, $actor, [
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
