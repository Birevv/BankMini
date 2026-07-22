<?php

namespace App\Actions\Accounting;

use App\Enums\JournalPosition;
use App\Enums\TransactionType;
use App\Exceptions\UnbalancedJournalException;
use App\Models\Transaksi;

class GenerateJournalAction
{
    public function execute(Transaksi $transaction): void
    {
        if ((! $transaction->exists) || $transaction->nominal <= 0) {
            throw new UnbalancedJournalException;
        }

        $entries = $this->entriesFor($transaction);

        $transaction->journals()->createMany($entries);

        $createdEntries = $transaction->journals()->get();
        $debit = $createdEntries->where('posisi', JournalPosition::DEBIT)->sum('jumlah');
        $credit = $createdEntries->where('posisi', JournalPosition::CREDIT)->sum('jumlah');

        if ($createdEntries->count() !== 2 || $debit !== $credit) {
            throw new UnbalancedJournalException;
        }
    }

    /** @return list<array{kode_akun: string, posisi: JournalPosition, jumlah: int}> */
    private function entriesFor(Transaksi $transaction): array
    {
        $cashAccount = (string) config('bank.accounts.cash');
        $savingsAccount = (string) config('bank.accounts.customer_savings');

        return match ($transaction->jenis_trans) {
            TransactionType::DEPOSIT => [
                ['kode_akun' => $cashAccount, 'posisi' => JournalPosition::DEBIT, 'jumlah' => $transaction->nominal],
                ['kode_akun' => $savingsAccount, 'posisi' => JournalPosition::CREDIT, 'jumlah' => $transaction->nominal],
            ],
            TransactionType::WITHDRAWAL => [
                ['kode_akun' => $savingsAccount, 'posisi' => JournalPosition::DEBIT, 'jumlah' => $transaction->nominal],
                ['kode_akun' => $cashAccount, 'posisi' => JournalPosition::CREDIT, 'jumlah' => $transaction->nominal],
            ],
        };
    }
}
