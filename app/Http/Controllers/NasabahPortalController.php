<?php

namespace App\Http\Controllers;

use App\Http\Requests\NasabahStatementRequest;
use App\Models\Nasabah;
use App\Services\BalanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class NasabahPortalController extends Controller
{
    public function __invoke(NasabahStatementRequest $request, BalanceService $balanceService): View
    {
        /** @var Nasabah $customer */
        $customer = auth('nasabah')->user();
        $filters = $request->validated();

        $transactions = $customer->transactions()
            ->with('teller:id,nama_petugas')
            ->when($filters['dari'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('tanggal', '>=', $date))
            ->when($filters['sampai'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('tanggal', '<=', $date))
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('nasabah.dashboard', [
            'customer' => $customer,
            'balance' => $balanceService->forCustomer((int) $customer->getKey()),
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }
}
