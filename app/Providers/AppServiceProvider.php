<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\JurnalAkuntansi;
use App\Models\LaporanHarianTeller;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\JurnalAkuntansiPolicy;
use App\Policies\LaporanHarianTellerPolicy;
use App\Policies\NasabahPolicy;
use App\Policies\TransaksiPolicy;
use App\Policies\UserPolicy;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Nasabah::class, NasabahPolicy::class);
        Gate::policy(Transaksi::class, TransaksiPolicy::class);
        Gate::policy(JurnalAkuntansi::class, JurnalAkuntansiPolicy::class);
        Gate::policy(LaporanHarianTeller::class, LaporanHarianTellerPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);

        $this->registerAuthenticationAuditListeners();
        $this->registerModelAuditListeners();
    }

    private function registerAuthenticationAuditListeners(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                app(AuditLogService::class)->record('auth.login.succeeded', $event->user, $event->user, [
                    'guard' => $event->guard,
                ]);
            }
        });

        Event::listen(Failed::class, function (Failed $event): void {
            if ($event->guard !== 'web') {
                return;
            }

            app(AuditLogService::class)->record('auth.login.failed', $event->user instanceof User ? $event->user : null, null, [
                'guard' => $event->guard,
                'username' => $event->credentials['username'] ?? null,
            ]);
        });
    }

    private function registerModelAuditListeners(): void
    {
        User::created(fn (User $user) => app(AuditLogService::class)->record(
            'user.created',
            $user,
            auth()->user() instanceof User ? auth()->user() : null,
            ['role' => $user->role->value],
        ));

        User::updated(fn (User $user) => app(AuditLogService::class)->record(
            'user.updated',
            $user,
            auth()->user() instanceof User ? auth()->user() : null,
            ['changes' => $user->getChanges()],
        ));

        Nasabah::created(fn (Nasabah $customer) => app(AuditLogService::class)->record(
            'customer.created',
            $customer,
            auth()->user() instanceof User ? auth()->user() : null,
            ['account_number' => $customer->no_rekening, 'nis' => $customer->nis],
        ));

        Nasabah::updated(function (Nasabah $customer): void {
            $action = $customer->wasChanged('pin_keamanan') ? 'customer.pin.reset' : 'customer.updated';

            app(AuditLogService::class)->record(
                $action,
                $customer,
                auth()->user() instanceof User ? auth()->user() : null,
                ['changed_fields' => array_keys($customer->getChanges())],
            );
        });
    }
}
