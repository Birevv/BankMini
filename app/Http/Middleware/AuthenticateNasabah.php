<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateNasabah
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = auth('nasabah');

        if (! $guard->check()) {
            return redirect()->route('nasabah.login');
        }

        if (! $guard->user()->is_active) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('nasabah.login')->withErrors([
                'no_rekening' => 'Akun portal tidak aktif.',
            ]);
        }

        return $next($request);
    }
}
