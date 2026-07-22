<?php

namespace App\Actions\DailyReports;

use App\Enums\DailyReportStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Exceptions\UnauthorizedTransactionException;
use App\Models\LaporanHarianTeller;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use LogicException;

class CloseDailyCashAction
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function execute(
        User $teller,
        CarbonInterface $date,
        mixed $openingBalance,
        mixed $physicalBalance,
        ?string $notes = null,
    ): LaporanHarianTeller {
        if ((! $teller->is_active) || $teller->role !== UserRole::TELLER) {
            throw new UnauthorizedTransactionException;
        }

        $normalizedOpeningBalance = Money::fromInput($openingBalance, 'saldo_awal', allowZero: true);
        $normalizedPhysicalBalance = Money::fromInput($physicalBalance, 'saldo_fisik', allowZero: true);

        return DB::transaction(function () use (
            $teller,
            $date,
            $normalizedOpeningBalance,
            $normalizedPhysicalBalance,
            $notes,
        ): LaporanHarianTeller {
            $transactionQuery = Transaksi::query()
                ->forTeller((int) $teller->getKey())
                ->whereDate('tanggal', $date->toDateString());

            $totalDeposits = (int) (clone $transactionQuery)
                ->where('jenis_trans', TransactionType::DEPOSIT->value)
                ->sum('nominal');
            $totalWithdrawals = (int) (clone $transactionQuery)
                ->where('jenis_trans', TransactionType::WITHDRAWAL->value)
                ->sum('nominal');
            $systemClosingBalance = $normalizedOpeningBalance + $totalDeposits - $totalWithdrawals;

            if ($systemClosingBalance < 0) {
                throw new LogicException('Saldo akhir sistem tidak boleh negatif.');
            }

            $report = LaporanHarianTeller::query()
                ->where('id_teller', $teller->getKey())
                ->whereDate('tanggal', $date->toDateString())
                ->lockForUpdate()
                ->first();

            if ($report && in_array($report->status, [DailyReportStatus::SUBMITTED, DailyReportStatus::APPROVED], true)) {
                throw new LogicException('Laporan yang sudah diajukan tidak dapat diubah.');
            }

            $report ??= new LaporanHarianTeller;
            $report->fill([
                'id_teller' => $teller->getKey(),
                'tanggal' => $date->toDateString(),
                'saldo_awal' => $normalizedOpeningBalance,
                'total_setoran' => $totalDeposits,
                'total_penarikan' => $totalWithdrawals,
                'saldo_akhir_sistem' => $systemClosingBalance,
                'saldo_fisik' => $normalizedPhysicalBalance,
                'selisih' => $normalizedPhysicalBalance - $systemClosingBalance,
                'status' => DailyReportStatus::DRAFT,
                'catatan_teller' => filled($notes) ? $notes : null,
                'catatan_supervisor' => null,
                'approved_by' => null,
                'approved_at' => null,
            ])->save();

            $this->auditLog->record('daily_report.draft.saved', $report, $teller, [
                'date' => $date->toDateString(),
                'difference' => $report->selisih,
            ]);

            return $report->fresh(['teller']);
        }, attempts: 3);
    }
}
