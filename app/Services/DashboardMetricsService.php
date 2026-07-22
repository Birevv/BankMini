<?php

namespace App\Services;

use App\Enums\DailyReportStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\LaporanHarianTeller;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardMetricsService
{
    /** @return array{customers: int, total_balance: int, active_staff: int, transactions_today: int} */
    public function admin(): array
    {
        return [
            'customers' => Nasabah::query()->where('is_active', true)->count(),
            'total_balance' => $this->totalDeposits() - $this->totalWithdrawals(),
            'active_staff' => User::query()
                ->where('is_active', true)
                ->whereIn('role', [UserRole::TELLER->value, UserRole::SUPERVISOR->value])
                ->count(),
            'transactions_today' => $this->transactionsToday()->count(),
        ];
    }

    /**
     * @return array{
     *     metrics: array{customers: int, total_balance: int, active_staff: int, transactions_today: int},
     *     daily_flow: list<array{label: string, deposits: int, withdrawals: int}>,
     *     recent_transactions: Collection<int, Transaksi>
     * }
     */
    public function adminDashboard(): array
    {
        $startDate = today()->subDays(6)->startOfDay();
        $endDate = today()->endOfDay();

        $dailyTotals = Transaksi::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal) as transaction_date')
            ->selectRaw(
                'SUM(CASE WHEN jenis_trans = ? THEN nominal ELSE 0 END) as deposits',
                [TransactionType::DEPOSIT->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN jenis_trans = ? THEN nominal ELSE 0 END) as withdrawals',
                [TransactionType::WITHDRAWAL->value],
            )
            ->groupBy('transaction_date')
            ->get()
            ->keyBy('transaction_date');

        $dailyFlow = [];

        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $date = $startDate->copy()->addDays($dayOffset);
            $totals = $dailyTotals->get($date->toDateString());

            $dailyFlow[] = [
                'label' => $date->locale('id')->translatedFormat('D'),
                'deposits' => (int) ($totals?->deposits ?? 0),
                'withdrawals' => (int) ($totals?->withdrawals ?? 0),
            ];
        }

        return [
            'metrics' => $this->admin(),
            'daily_flow' => $dailyFlow,
            'recent_transactions' => Transaksi::query()
                ->with('nasabah:id,nama_siswa')
                ->latest('tanggal')
                ->limit(5)
                ->get(['id', 'id_nasabah', 'jenis_trans', 'nominal', 'tanggal']),
        ];
    }

    /** @return array{transactions: int, deposits: int, withdrawals: int, net_cash: int} */
    public function teller(User $teller): array
    {
        $transactions = $this->transactionsToday()->forTeller((int) $teller->getKey());
        $deposits = (int) (clone $transactions)
            ->where('jenis_trans', TransactionType::DEPOSIT->value)
            ->sum('nominal');
        $withdrawals = (int) (clone $transactions)
            ->where('jenis_trans', TransactionType::WITHDRAWAL->value)
            ->sum('nominal');

        return [
            'transactions' => (clone $transactions)->count(),
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
            'net_cash' => $deposits - $withdrawals,
        ];
    }

    /** @return array{transactions_today: int, turnover_today: int, pending_reports: int, approved_today: int} */
    public function supervisor(): array
    {
        return [
            'transactions_today' => $this->transactionsToday()->count(),
            'turnover_today' => (int) $this->transactionsToday()->sum('nominal'),
            'pending_reports' => LaporanHarianTeller::query()
                ->where('status', DailyReportStatus::SUBMITTED->value)
                ->count(),
            'approved_today' => LaporanHarianTeller::query()
                ->whereDate('approved_at', today())
                ->where('status', DailyReportStatus::APPROVED->value)
                ->count(),
        ];
    }

    private function totalDeposits(): int
    {
        return (int) Transaksi::query()
            ->where('jenis_trans', TransactionType::DEPOSIT->value)
            ->sum('nominal');
    }

    private function totalWithdrawals(): int
    {
        return (int) Transaksi::query()
            ->where('jenis_trans', TransactionType::WITHDRAWAL->value)
            ->sum('nominal');
    }

    private function transactionsToday(): Builder
    {
        return Transaksi::query()->whereBetween('tanggal', [today()->startOfDay(), today()->endOfDay()]);
    }
}
