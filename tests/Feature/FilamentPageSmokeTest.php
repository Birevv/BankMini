<?php

namespace Tests\Feature;

use App\Actions\DailyReports\CloseDailyCashAction;
use App\Actions\Transactions\CreateDepositAction;
use App\Filament\Admin\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\AuditLog;
use App\Models\Nasabah;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Livewire\Livewire;
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
            ->assertSee(Vite::asset('resources/css/filament-solid.css'), false)
            ->assertSee('/js/filament/filament/app.js', false)
            ->assertSee('Lewati ke konten')
            ->assertSee('Dashboard Administrator')
            ->assertSee('Nasabah aktif')
            ->assertSee('Arus transaksi 7 hari')
            ->assertSee('Aktivitas terbaru')
            ->assertSee('bank-admin-widget', false)
            ->assertSee('wire:navigate', false)
            ->assertDontSee('fi-global-search-ctn', false)
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
            ->assertSee('wire:navigate', false)
            ->assertSee('--sidebar-width: 17rem', false)
            ->assertSee('fi-body-has-sidebar-collapsible-on-desktop', false)
            ->assertDontSee('fi-global-search-ctn', false)
            ->assertSee(Vite::asset('resources/css/filament-solid.css'), false);

        $this->actingAs($supervisor)
            ->get('/supervisor')
            ->assertOk()
            ->assertSee('Dashboard Supervisor')
            ->assertSee('Ringkasan pengawasan')
            ->assertSee('wire:navigate', false)
            ->assertSee('--sidebar-width: 17rem', false)
            ->assertSee('fi-body-has-sidebar-collapsible-on-desktop', false)
            ->assertDontSee('fi-global-search-ctn', false)
            ->assertSee(Vite::asset('resources/css/filament-solid.css'), false);
    }

    public function testAdminPagesRender(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([
            '/admin/petugas' => 'petugas terdaftar',
            '/admin/nasabah' => 'nasabah terdaftar',
            '/admin/transaksi' => 'transaksi tercatat',
            '/admin/jurnal-akuntansi' => 'entri jurnal tercatat',
            '/admin/audit-log' => 'aktivitas tercatat',
        ] as $uri => $recordLabel) {
            $this->actingAs($admin)
                ->get($uri)
                ->assertOk()
                ->assertSee('bank-data-table', false)
                ->assertSee('bank-table-record-count', false)
                ->assertSee($recordLabel)
                ->assertSee('bank-table-filter-trigger', false)
                ->assertSee('data-active-filters-count="0"', false);
        }

        foreach (['/admin/petugas/create', '/admin/nasabah/create'] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    public function testAuditLogPageUsesReadableActivityPresentation(): void
    {
        $admin = User::factory()->admin()->create();
        DB::table('audit_logs')->delete();

        $record = AuditLog::query()->create([
            'actor_id' => $admin->id,
            'action' => 'auth.login.succeeded',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'metadata' => [
                'user_agent' => 'Feature Test Browser',
                'before' => ['is_active' => false],
                'after' => ['is_active' => true],
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/audit-log')
            ->assertOk()
            ->assertSee('Riwayat aktivitas pengguna dan perubahan penting di dalam sistem.')
            ->assertSee('1 aktivitas tercatat')
            ->assertSee('bank-table-filter-trigger', false)
            ->assertSee('data-active-filters-count="0"', false);

        AuditLog::query()->create([
            'action' => 'customer_portal.login.failed',
            'metadata' => ['account_number' => 'BM-0001'],
            'ip_address' => '127.0.0.2',
            'created_at' => now()->addSecond(),
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $component = Livewire::actingAs($admin)
            ->test(ListAuditLogs::class)
            ->loadTable()
            ->assertSee('Login administrator berhasil')
            ->assertSee('Login nasabah gagal')
            ->assertSee('Sistem / Nasabah')
            ->assertSee('audit-log-system-actor', false)
            ->assertSee('fi-color-danger', false)
            ->assertSee('Lihat detail');

        $component
            ->filterTable('action', 'auth.login.succeeded')
            ->assertSee('data-active-filters-count="1"', false);

        $this->view('filament.shared.audit-log-detail', ['record' => $record])
            ->assertSee('Event code asli')
            ->assertSee('auth.login.succeeded')
            ->assertSee('Feature Test Browser')
            ->assertSee('Nilai sebelum perubahan')
            ->assertSee('Nilai setelah perubahan');
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

        $this->actingAs($teller)
            ->get('/teller/riwayat-transaksi')
            ->assertOk()
            ->assertSee('bank-data-table', false)
            ->assertSee('transaksi tercatat')
            ->assertSee('bank-table-filter-trigger', false);
    }

    public function testSupervisorPagesRender(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        foreach ([
            '/supervisor/transaksi' => 'transaksi tercatat',
            '/supervisor/jurnal-akuntansi' => 'entri jurnal tercatat',
            '/supervisor/laporan-harian' => 'laporan tersedia',
            '/supervisor/audit-log' => 'aktivitas tercatat',
        ] as $uri => $recordLabel) {
            $this->actingAs($supervisor)
                ->get($uri)
                ->assertOk()
                ->assertSee('bank-data-table', false)
                ->assertSee($recordLabel)
                ->assertSee('bank-table-filter-trigger', false);
        }

        $this->actingAs($supervisor)
            ->get('/supervisor/audit-log')
            ->assertOk()
            ->assertSee('Riwayat aktivitas pengguna dan perubahan penting di dalam sistem.')
            ->assertSee('bank-table-record-count', false)
            ->assertSee('data-active-filters-count="0"', false);
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
