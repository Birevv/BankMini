<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR {{ $customer->no_rekening }}</title>
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="print-page">
    <main class="qr-card">
        <div class="document-brand"><span class="brand-mark" aria-hidden="true">BM</span><span>{{ config('bank.name') }}</span></div>
        <span class="eyebrow">Kartu Rekening</span>
        <h1>Identitas rekening</h1>
        <img src="{{ $qrCode }}" alt="QR Code rekening {{ $customer->no_rekening }}">
        <h2>{{ $customer->nama_siswa }}</h2>
        <p class="account-number">{{ $customer->no_rekening }}</p>
        <p>QR hanya berisi nomor rekening dan tidak mengandung PIN atau kata sandi.</p>
        <button class="button button-primary print-hidden" type="button" data-print-button>Cetak Kartu</button>
    </main>
    <script src="{{ asset('js/print.js') }}" defer></script>
</body>
</html>
