<?php

namespace App\Providers\Filament;

use App\Filament\Supervisor\Pages\Dashboard;
use App\Filament\Supervisor\Widgets\SupervisorOverview;
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

class SupervisorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supervisor')
            ->path('supervisor')
            ->brandName('Bank Mini · Supervisor')
            ->login(RedirectToInternalLoginController::class)
            ->colors(['primary' => Color::Emerald])
            ->darkMode(false)
            ->themeSwitcher(false)
            ->globalSearch(false)
            ->spa()
            ->viteTheme('resources/css/filament-solid.css')
            ->maxContentWidth(Width::Full)
            ->profile()
            ->sidebarWidth('17rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Supervisor/Resources'), for: 'App\\Filament\\Supervisor\\Resources')
            ->discoverPages(in: app_path('Filament/Supervisor/Pages'), for: 'App\\Filament\\Supervisor\\Pages')
            ->pages([Dashboard::class])
            ->widgets([SupervisorOverview::class])
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
