<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\SupervisorPanelProvider;
use App\Providers\Filament\TellerPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    TellerPanelProvider::class,
    SupervisorPanelProvider::class,
];
