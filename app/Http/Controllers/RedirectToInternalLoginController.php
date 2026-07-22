<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RedirectToInternalLoginController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('internal.login');
    }
}
