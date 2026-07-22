<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="same-origin">
    <title>@yield('title', 'Portal Nasabah') · {{ config('bank.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="portal-page">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="site-header">
        <a class="brand-lockup" href="{{ route('home') }}">
            <span class="brand-mark" aria-hidden="true">BM</span>
            <span class="brand-copy"><strong>{{ config('bank.name') }}</strong><small>Portal Nasabah</small></span>
        </a>
        @auth('nasabah')
            <nav class="portal-nav" aria-label="Navigasi portal">
                <a href="{{ route('nasabah.dashboard') }}">Ringkasan rekening</a>
                <form method="POST" action="{{ route('nasabah.logout') }}">
                    @csrf
                    <button class="text-button" type="submit">Keluar</button>
                </form>
            </nav>
        @endauth
    </header>
    <main class="portal-shell" id="main-content">
        @yield('content')
    </main>
    <script src="{{ asset('js/form-safety.js') }}" defer></script>
</body>
</html>
