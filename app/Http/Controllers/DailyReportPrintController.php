<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarianTeller;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DailyReportPrintController extends Controller
{
    public function __invoke(LaporanHarianTeller $report): View
    {
        Gate::authorize('view', $report);
        $report->loadMissing(['teller', 'supervisor']);

        return view('reports.daily', ['report' => $report]);
    }
}
