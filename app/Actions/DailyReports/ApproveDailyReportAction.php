<?php

namespace App\Actions\DailyReports;

use App\Enums\DailyReportStatus;
use App\Enums\UserRole;
use App\Exceptions\DailyReportMismatchException;
use App\Models\LaporanHarianTeller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use LogicException;

class ApproveDailyReportAction
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function execute(LaporanHarianTeller $report, User $actor): LaporanHarianTeller
    {
        $this->ensureSupervisor($actor, $report);

        return DB::transaction(function () use ($report, $actor): LaporanHarianTeller {
            $lockedReport = LaporanHarianTeller::query()->lockForUpdate()->findOrFail($report->getKey());

            if ($lockedReport->status !== DailyReportStatus::SUBMITTED) {
                throw new LogicException('Hanya laporan yang sudah diajukan yang dapat disetujui.');
            }

            if ($lockedReport->selisih !== 0) {
                throw new DailyReportMismatchException;
            }

            $lockedReport->update([
                'status' => DailyReportStatus::APPROVED,
                'approved_by' => $actor->getKey(),
                'approved_at' => now(),
                'catatan_supervisor' => null,
            ]);
            $this->auditLog->record('daily_report.approved', $lockedReport, $actor);

            return $lockedReport->fresh(['teller', 'supervisor']);
        });
    }

    private function ensureSupervisor(User $actor, LaporanHarianTeller $report): void
    {
        if (
            $actor->role !== UserRole::SUPERVISOR
            || (! $actor->is_active)
            || $report->id_teller === $actor->getKey()
        ) {
            throw new AuthorizationException('Hanya Supervisor aktif yang dapat menyetujui laporan Teller.');
        }
    }
}
