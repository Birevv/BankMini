<?php

namespace App\Policies;

use App\Enums\DailyReportStatus;
use App\Enums\UserRole;
use App\Models\LaporanHarianTeller;
use App\Models\User;

class LaporanHarianTellerPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && in_array($actor->role, UserRole::cases(), true);
    }

    public function view(User $actor, LaporanHarianTeller $report): bool
    {
        return $this->viewAny($actor)
            && ($actor->role !== UserRole::TELLER || $report->id_teller === $actor->getKey());
    }

    public function create(User $actor): bool
    {
        return $actor->is_active && $actor->role === UserRole::TELLER;
    }

    public function update(User $actor, LaporanHarianTeller $report): bool
    {
        return $this->create($actor)
            && $report->id_teller === $actor->getKey()
            && in_array($report->status, [DailyReportStatus::DRAFT, DailyReportStatus::REJECTED], true);
    }

    public function approve(User $actor, LaporanHarianTeller $report): bool
    {
        return $actor->is_active
            && $actor->role === UserRole::SUPERVISOR
            && $report->id_teller !== $actor->getKey()
            && $report->status === DailyReportStatus::SUBMITTED
            && $report->selisih === 0;
    }

    public function reject(User $actor, LaporanHarianTeller $report): bool
    {
        return $actor->is_active
            && $actor->role === UserRole::SUPERVISOR
            && $report->id_teller !== $actor->getKey()
            && $report->status === DailyReportStatus::SUBMITTED;
    }

    public function delete(User $actor, LaporanHarianTeller $report): bool
    {
        return false;
    }

    public function deleteAny(User $actor): bool
    {
        return false;
    }

    public function restore(User $actor, LaporanHarianTeller $report): bool
    {
        return false;
    }

    public function restoreAny(User $actor): bool
    {
        return false;
    }

    public function forceDelete(User $actor, LaporanHarianTeller $report): bool
    {
        return false;
    }

    public function forceDeleteAny(User $actor): bool
    {
        return false;
    }

    public function replicate(User $actor, LaporanHarianTeller $report): bool
    {
        return false;
    }

    public function reorder(User $actor): bool
    {
        return false;
    }
}
