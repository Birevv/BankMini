<?php

namespace App\Providers\Filament;

use App\Filament\Teller\Pages\Dashboard;
use App\Filament\Teller\Widgets\TellerOverview;
use App\Http\Controllers\RedirectToInternalLoginController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teller')
            ->path('teller')
            ->brandName('Bank Mini · Teller')
            ->login(RedirectToInternalLoginController::class)
            ->colors(['primary' => Color::Emerald])
            ->darkMode(false)
            ->themeSwitcher(false)
            ->theme('bank-mini-panel-theme')
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Teller/Resources'), for: 'App\\Filament\\Teller\\Resources')
            ->discoverPages(in: app_path('Filament/Teller/Pages'), for: 'App\\Filament\\Teller\\Pages')
            ->pages([Dashboard::class])
            ->widgets([TellerOverview::class])
            ->strictAuthorization()
            ->middleware($this->middleware())
            ->authMiddleware([Authenticate::class]);
    }

    /** @return list<class-string> */
    private function middleware(): array
    {
        return [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }
}
