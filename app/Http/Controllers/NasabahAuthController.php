<?php

namespace App\Http\Controllers;

use App\Http\Requests\NasabahLoginRequest;
use App\Models\Nasabah;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NasabahAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth('nasabah')->check()) {
            return redirect()->route('nasabah.dashboard');
        }

        return view('nasabah.login');
    }

    public function store(NasabahLoginRequest $request, AuditLogService $auditLog): RedirectResponse
    {
        $key = 'nasabah-login:'.strtolower($request->string('no_rekening')->toString()).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'no_rekening' => 'Terlalu banyak percobaan. Silakan coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        $accountNumber = $request->string('no_rekening')->trim()->upper()->toString();
        $credentials = [
            'no_rekening' => $accountNumber,
            'password' => $request->string('password')->toString(),
            'is_active' => true,
        ];

        if (! auth('nasabah')->attempt($credentials)) {
            RateLimiter::hit($key, 60);
            $customer = Nasabah::query()->where('no_rekening', $accountNumber)->first();
            $auditLog->record('customer_portal.login.failed', $customer, null, ['account_number' => $accountNumber]);

            throw ValidationException::withMessages([
                'no_rekening' => 'Nomor rekening atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        /** @var Nasabah $customer */
        $customer = auth('nasabah')->user();
        $auditLog->record('customer_portal.login.succeeded', $customer, null);

        return redirect()->intended(route('nasabah.dashboard'));
    }

    public function destroy(Request $request, AuditLogService $auditLog): RedirectResponse
    {
        /** @var Nasabah|null $customer */
        $customer = auth('nasabah')->user();
        $auditLog->record('customer_portal.logout', $customer, null);
        auth('nasabah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('nasabah.login');
    }
}
