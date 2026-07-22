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

class SubmitDailyReportAction
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function execute(LaporanHarianTeller $report, User $actor): LaporanHarianTeller
    {
        if ($actor->role !== UserRole::TELLER || (! $actor->is_active) || $report->id_teller !== $actor->getKey()) {
            throw new AuthorizationException('Hanya Teller pemilik laporan yang dapat mengajukan laporan.');
        }

        return DB::transaction(function () use ($report, $actor): LaporanHarianTeller {
            $lockedReport = LaporanHarianTeller::query()->lockForUpdate()->findOrFail($report->getKey());

            if ($lockedReport->status !== DailyReportStatus::DRAFT) {
                throw new LogicException('Hanya laporan berstatus draf yang dapat diajukan.');
            }

            if ($lockedReport->selisih !== 0) {
                throw new DailyReportMismatchException;
            }

            $lockedReport->update(['status' => DailyReportStatus::SUBMITTED]);
            $this->auditLog->record('daily_report.submitted', $lockedReport, $actor);

            return $lockedReport->fresh(['teller']);
        });
    }
}
