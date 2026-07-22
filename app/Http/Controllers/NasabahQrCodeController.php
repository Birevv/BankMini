<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NasabahQrCodeController extends Controller
{
    public function __invoke(Nasabah $nasabah, QrCodeService $qrCodeService): View
    {
        Gate::authorize('update', $nasabah);

        return view('nasabah.qr-code', [
            'customer' => $nasabah,
            'qrCode' => $qrCodeService->accountDataUri($nasabah->no_rekening),
        ]);
    }
}
