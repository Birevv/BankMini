<?php

namespace App\Http\Controllers;

use App\Http\Requests\InternalLoginRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InternalAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user instanceof User) {
            return redirect()->to(Filament::getPanel($user->role->value)->getUrl());
        }

        return view('auth.internal-login');
    }

    public function store(InternalLoginRequest $request): RedirectResponse
    {
        $key = 'internal-login:'.strtolower($request->string('username')->toString()).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'username' => 'Terlalu banyak percobaan. Silakan coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        $authenticated = auth()->attempt([
            'username' => $request->string('username')->trim()->toString(),
            'password' => $request->string('password')->toString(),
            'is_active' => true,
        ], $request->boolean('remember'));

        if (! $authenticated) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'username' => 'Username atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        /** @var User $user */
        $user = auth()->user();

        return redirect()->intended(Filament::getPanel($user->role->value)->getUrl());
    }
}
