<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <title>@yield('title') · {{ config('bank.name') }}</title>
    @include('partials.public-font')
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="auth-page">
    <a class="skip-link" href="#auth-content">Lewati ke formulir masuk</a>
    <div class="auth-layout">
        <aside class="auth-aside">
            <a class="brand-lockup auth-brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">BM</span>
                <span class="brand-copy"><strong>{{ config('bank.name') }}</strong><small>@yield('brand-role')</small></span>
            </a>
            <div class="auth-aside-content">
                @yield('aside')
            </div>
            <small class="auth-aside-footer">Sistem Informasi E-Teller · Akses terproteksi</small>
        </aside>
        <main class="auth-main" id="auth-content">
            <section class="auth-card">
                @yield('content')
            </section>
            <a class="auth-back-link" href="{{ route('home') }}">Kembali ke halaman utama</a>
        </main>
    </div>
    <script src="{{ asset('js/form-safety.js') }}" defer></script>
    <script src="{{ asset('js/page-transitions.js') }}" defer></script>
</body>
</html>
