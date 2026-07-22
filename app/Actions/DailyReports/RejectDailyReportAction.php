<?php

namespace App\Actions\DailyReports;

use App\Enums\DailyReportStatus;
use App\Enums\UserRole;
use App\Models\LaporanHarianTeller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class RejectDailyReportAction
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function execute(LaporanHarianTeller $report, User $actor, string $notes): LaporanHarianTeller
    {
        if ($actor->role !== UserRole::SUPERVISOR || (! $actor->is_active) || $report->id_teller === $actor->getKey()) {
            throw new AuthorizationException('Hanya Supervisor aktif yang dapat menolak laporan Teller.');
        }

        if (blank(trim($notes))) {
            throw ValidationException::withMessages([
                'catatan_supervisor' => 'Catatan penolakan wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($report, $actor, $notes): LaporanHarianTeller {
            $lockedReport = LaporanHarianTeller::query()->lockForUpdate()->findOrFail($report->getKey());

            if ($lockedReport->status !== DailyReportStatus::SUBMITTED) {
                throw new LogicException('Hanya laporan yang sudah diajukan yang dapat ditolak.');
            }

            $lockedReport->update([
                'status' => DailyReportStatus::REJECTED,
                'catatan_supervisor' => trim($notes),
                'approved_by' => null,
                'approved_at' => null,
            ]);
            $this->auditLog->record('daily_report.rejected', $lockedReport, $actor, [
                'reason' => trim($notes),
            ]);

            return $lockedReport->fresh(['teller']);
        });
    }
}
