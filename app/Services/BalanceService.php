<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Builder;

class BalanceService
{
    public function forCustomer(int $customerId): int
    {
        return $this->fromQuery(
            Transaksi::query()->forCustomer($customerId),
        );
    }

    public function afterTransaction(Transaksi $transaction): int
    {
        $query = Transaksi::query()
            ->forCustomer((int) $transaction->id_nasabah)
            ->where(function (Builder $query) use ($transaction): void {
                $query
                    ->where('tanggal', '<', $transaction->tanggal)
                    ->orWhere(function (Builder $query) use ($transaction): void {
                        $query
                            ->where('tanggal', $transaction->tanggal)
                            ->where('id', '<=', $transaction->getKey());
                    });
            });

        return $this->fromQuery($query);
    }

    private function fromQuery(Builder $query): int
    {
        $deposits = (clone $query)
            ->where('jenis_trans', TransactionType::DEPOSIT->value)
            ->sum('nominal');
        $withdrawals = (clone $query)
            ->where('jenis_trans', TransactionType::WITHDRAWAL->value)
            ->sum('nominal');

        return (int) $deposits - (int) $withdrawals;
    }
}
