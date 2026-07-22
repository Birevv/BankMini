<?php

namespace App\Filament\Admin\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\Widget;

class AdminOverview extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.admin.widgets.admin-overview';

    protected int|string|array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return app(DashboardMetricsService::class)->adminDashboard();
    }
}
