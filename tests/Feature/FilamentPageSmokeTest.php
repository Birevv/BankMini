<?php

namespace Tests\Feature;

use App\Actions\DailyReports\CloseDailyCashAction;
use App\Actions\Transactions\CreateDepositAction;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminDashboardLoadsFilamentAssetsAndIndonesianLayoutTranslation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('/css/app/bank-mini-panel-theme.css', false)
            ->assertSee('/js/filament/filament/app.js', false)
            ->assertSee('Lewati ke konten')
            ->assertSee('Dashboard Administrator')
            ->assertSee('Nasabah aktif')
            ->assertSee('Arus transaksi 7 hari')
            ->assertSee('Aktivitas terbaru')
            ->assertSee('bank-admin-widget', false)
            ->assertDontSee('filament-panels::layout.skip_to_content.label');
    }

    public function testTellerAndSupervisorDashboardsUseRoleSpecificOverview(): void
    {
        $teller = User::factory()->teller()->create();
        $supervisor = User::factory()->supervisor()->create();

        $this->actingAs($teller)
            ->get('/teller')
            ->assertOk()
            ->assertSee('Dashboard Teller')
            ->assertSee('Aktivitas loket hari ini')
            ->assertSee('/css/app/bank-mini-panel-theme.css', false);

        $this->actingAs($supervisor)
            ->get('/supervisor')
            ->assertOk()
            ->assertSee('Dashboard Supervisor')
            ->assertSee('Ringkasan pengawasan')
            ->assertSee('/css/app/bank-mini-panel-theme.css', false);
    }

    public function testAdminPagesRender(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([
            '/admin/petugas',
            '/admin/petugas/create',
            '/admin/nasabah',
            '/admin/nasabah/create',
            '/admin/transaksi',
            '/admin/jurnal-akuntansi',
            '/admin/audit-log',
        ] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    public function testTellerPagesRender(): void
    {
        $teller = User::factory()->teller()->create();

        foreach ([
            '/teller/cari-nasabah',
            '/teller/setoran',
            '/teller/penarikan',
            '/teller/laporan-harian',
            '/teller/riwayat-transaksi',
        ] as $uri) {
            $this->actingAs($teller)->get($uri)->assertOk();
        }
    }

    public function testSupervisorPagesRender(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        foreach ([
            '/supervisor/transaksi',
            '/supervisor/jurnal-akuntansi',
            '/supervisor/laporan-harian',
            '/supervisor/audit-log',
        ] as $uri) {
            $this->actingAs($supervisor)->get($uri)->assertOk();
        }
    }

    public function testPrintableArtifactsRenderWithoutSensitiveCredentials(): void
    {
        $admin = User::factory()->admin()->create();
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        $transaction = app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);
        $report = app(CloseDailyCashAction::class)->execute($teller, today(), 0, 50_000);

        $this->actingAs($admin)
            ->get(route('admin.nasabah.qr', $customer))
            ->assertOk()
            ->assertDontSee('123456')
            ->assertDontSee('nasabah123');

        $this->actingAs($admin)
            ->get(route('transactions.receipt', $transaction))
            ->assertOk()
            ->assertDontSee('123456')
            ->assertDontSee('nasabah123');

        $this->actingAs($teller)
            ->get(route('daily-reports.print', $report))
            ->assertOk();
    }
}
