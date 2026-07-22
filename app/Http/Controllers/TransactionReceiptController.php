<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionReceiptController extends Controller
{
    public function __invoke(Transaksi $transaction, BalanceService $balanceService): View
    {
        Gate::authorize('view', $transaction);
        $transaction->loadMissing(['nasabah', 'teller']);

        return view('receipts.transaction', [
            'transaction' => $transaction,
            'balanceAfter' => $balanceService->afterTransaction($transaction),
        ]);
    }
}
