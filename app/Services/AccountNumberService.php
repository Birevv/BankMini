<?php

namespace App\Services;

use App\Models\Nasabah;

class AccountNumberService
{
    public function generate(): string
    {
        $year = now()->format('Y');
        $prefix = sprintf('%s-%s-', config('bank.account_prefix'), $year);
        $lastAccountNumber = Nasabah::query()
            ->where('no_rekening', 'like', $prefix.'%')
            ->lockForUpdate()
            ->latest('no_rekening')
            ->value('no_rekening');

        $nextSequence = $lastAccountNumber
            ? ((int) str($lastAccountNumber)->afterLast('-')->toString()) + 1
            : 1;

        return $prefix.str_pad((string) $nextSequence, 6, '0', STR_PAD_LEFT);
    }
}
