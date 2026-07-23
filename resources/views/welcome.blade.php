<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <title>{{ config('bank.name') }}</title>
    @include('partials.public-font')
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="landing-page">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="landing-header">
        <a class="brand-lockup" href="{{ route('home') }}" aria-label="{{ config('bank.name') }} — halaman utama">
            <span class="brand-mark" aria-hidden="true">BM</span>
            <span class="brand-copy"><strong>{{ config('bank.name') }}</strong><small>Sistem Informasi E-Teller</small></span>
        </a>
        <nav class="landing-nav" aria-label="Navigasi utama">
            <a href="#layanan">Layanan</a>
            <a href="#cara-kerja">Cara kerja</a>
            <a class="button button-compact button-secondary" href="{{ route('internal.login') }}">Login petugas</a>
            <a class="button button-compact button-primary" href="{{ route('nasabah.login') }}">Portal nasabah</a>
        </nav>
    </header>
    <main class="landing-shell" id="main-content">
        <section class="hero-card" aria-labelledby="hero-title">
            <div class="hero-copy">
                <span class="eyebrow">Menabung · Disiplin · Mandiri</span>
                <h1 id="hero-title">Menabung di sekolah, lebih mudah dan aman.</h1>
                <p>Pantau saldo dan mutasi rekening secara transparan. Setoran dan penarikan dilayani petugas sekolah dengan proses yang tercatat permanen.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="{{ route('nasabah.login') }}">Masuk portal nasabah</a>
                    <a class="button button-secondary" href="{{ route('internal.login') }}">Login sebagai petugas</a>
                </div>
                <p class="hero-proof">Ledger permanen <span aria-hidden="true">•</span> QR tanpa PIN <span aria-hidden="true">•</span> Akses berbasis peran</p>
            </div>
            <aside class="program-card" aria-label="Program Bank Mini Sekolah">
                <div class="program-card-header"><span>PROGRAM BANK MINI SEKOLAH</span><small>Belajar · Menabung · Bertumbuh</small></div>
                <h2>Kebiasaan kecil, dampak besar.</h2>
                <p>Dari Mini membantu siswa belajar disiplin, mandiri, dan bertanggung jawab terhadap keuangan sejak sekolah.</p>
                <div class="program-values">
                    <div><span aria-hidden="true">✓</span><strong>Tercatat</strong><small>Ledger transaksi permanen</small></div>
                    <div><span aria-hidden="true">↕</span><strong>Terjaga</strong><small>PIN tersimpan terenkripsi</small></div>
                    <div><span aria-hidden="true">↗</span><strong>Terarah</strong><small>Akses sesuai peran</small></div>
                </div>
            </aside>
        </section>
        <section class="trust-strip" aria-label="Jaminan layanan">
            <div><span>01</span><p><strong>Transaksi tercatat permanen</strong><small>Riwayat tidak dapat diubah atau dihapus.</small></p></div>
            <div><span>02</span><p><strong>Identitas rekening aman</strong><small>QR hanya berisi identitas, bukan PIN.</small></p></div>
            <div><span>03</span><p><strong>PIN enam digit terlindungi</strong><small>Disimpan sebagai hash dan tidak dapat dilihat.</small></p></div>
        </section>
        <section class="content-section" id="layanan" aria-labelledby="services-title">
            <div class="section-intro"><div><span class="eyebrow">Layanan utama</span><h2 id="services-title">Layanan yang mudah dipahami</h2><p>Akses informasi tabungan tanpa proses yang rumit.</p></div><span class="section-kicker">Untuk siswa, sekolah, dan petugas</span></div>
            <div class="feature-grid">
            <article class="feature-card"><span>KONTROL 01</span><h2>Ledger permanen</h2><p>Setoran dan penarikan tersimpan sebagai catatan yang tidak dapat diedit atau dihapus.</p></article>
            <article class="feature-card"><span>KONTROL 02</span><h2>Jurnal seimbang</h2><p>Setiap transaksi otomatis membentuk debit dan kredit dengan nilai yang sama.</p></article>
            <article class="feature-card"><span>KONTROL 03</span><h2>Akses terisolasi</h2><p>Nasabah hanya dapat melihat saldo dan mutasi rekening miliknya sendiri.</p></article>
            </div>
        </section>
        <section class="process-section" id="cara-kerja" aria-labelledby="process-title">
            <div class="process-intro"><span class="system-badge">Cara kerja</span><h2 id="process-title">Sederhana untuk nasabah, terkontrol untuk sekolah.</h2><p>Setiap transaksi diproses petugas berwenang dan divalidasi oleh sistem.</p></div>
            <ol class="process-list">
                <li><span>1</span><strong>Datang atau scan QR</strong><p>Teller menemukan rekening nasabah dengan nomor rekening atau QR aman.</p></li>
                <li><span>2</span><strong>Transaksi diproses</strong><p>Setoran dicatat; penarikan memerlukan PIN dan saldo minimum.</p></li>
                <li><span>3</span><strong>Pantau dari portal</strong><p>Saldo dan mutasi terbaru dapat dilihat langsung oleh nasabah.</p></li>
            </ol>
        </section>
        <section class="landing-cta">
            <div><span class="eyebrow">Portal terpisah dan aman</span><h2>Akses informasi tabunganmu dengan aman.</h2><p>Nasabah hanya dapat melihat rekening sendiri. Petugas internal masuk melalui panel sesuai perannya.</p></div>
            <div class="hero-actions"><a class="button button-secondary" href="{{ route('internal.login') }}">Login petugas</a><a class="button button-primary" href="{{ route('nasabah.login') }}">Buka portal nasabah</a></div>
        </section>
    </main>
    <footer class="landing-footer"><span>{{ config('bank.name') }}</span><small>© {{ now()->year }} · Sistem Informasi E-Teller Sekolah</small></footer>
    <script src="{{ asset('js/page-transitions.js') }}" defer></script>
</body>
</html>
