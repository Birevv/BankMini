<?php

namespace Tests\Feature;

use App\Actions\DailyReports\ApproveDailyReportAction;
use App\Actions\DailyReports\CloseDailyCashAction;
use App\Actions\DailyReports\SubmitDailyReportAction;
use App\Actions\Transactions\CreateDepositAction;
use App\Actions\Transactions\CreateWithdrawalAction;
use App\Enums\DailyReportStatus;
use App\Exceptions\DailyReportMismatchException;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class DailyReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function testDailyTotalsSubmissionAndApprovalAreCalculatedCorrectly(): void
    {
        $teller = User::factory()->teller()->create();
        $supervisor = User::factory()->supervisor()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);
        app(CreateWithdrawalAction::class)->execute($teller, $customer->id, 20_000, '123456');

        $report = app(CloseDailyCashAction::class)->execute($teller, today(), 100_000, 130_000, 'Kas sesuai.');

        $this->assertSame(50_000, $report->total_setoran);
        $this->assertSame(20_000, $report->total_penarikan);
        $this->assertSame(130_000, $report->saldo_akhir_sistem);
        $this->assertSame(0, $report->selisih);

        $report = app(SubmitDailyReportAction::class)->execute($report, $teller);
        $this->assertSame(DailyReportStatus::SUBMITTED, $report->status);

        $report = app(ApproveDailyReportAction::class)->execute($report, $supervisor);
        $this->assertSame(DailyReportStatus::APPROVED, $report->status);
        $this->assertSame($supervisor->id, $report->approved_by);
        $this->assertNotNull($report->approved_at);

        $this->expectException(LogicException::class);
        $report->update(['catatan_teller' => 'Tidak boleh berubah']);
    }

    public function testMismatchedDailyReportCannotBeSubmitted(): void
    {
        $teller = User::factory()->teller()->create();
        $report = app(CloseDailyCashAction::class)->execute($teller, today(), 100_000, 90_000);

        $this->expectException(DailyReportMismatchException::class);
        app(SubmitDailyReportAction::class)->execute($report, $teller);
    }

    public function testTellerCannotApproveOwnReport(): void
    {
        $teller = User::factory()->teller()->create();
        $report = app(CloseDailyCashAction::class)->execute($teller, today(), 0, 0);
        $report = app(SubmitDailyReportAction::class)->execute($report, $teller);

        $this->expectException(AuthorizationException::class);
        app(ApproveDailyReportAction::class)->execute($report, $teller);
    }
}
