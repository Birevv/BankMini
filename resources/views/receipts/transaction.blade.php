<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk TRX-{{ str_pad((string) $transaction->id, 8, '0', STR_PAD_LEFT) }}</title>
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="print-page">
    <main class="receipt">
        <header>
            <div class="document-brand"><span class="brand-mark" aria-hidden="true">BM</span><span>{{ config('bank.name') }}</span></div>
            <span class="eyebrow">Struk Transaksi Sistem</span>
            <h1>Bukti transaksi</h1>
            <p>TRX-{{ str_pad((string) $transaction->id, 8, '0', STR_PAD_LEFT) }}</p>
        </header>
        <dl class="receipt-list">
            <div><dt>Tanggal</dt><dd>{{ $transaction->tanggal->format('d M Y H:i:s') }}</dd></div>
            <div><dt>Rekening</dt><dd>{{ \App\Support\AccountNumber::mask($transaction->nasabah->no_rekening) }}</dd></div>
            <div><dt>Nasabah</dt><dd>{{ $transaction->nasabah->nama_siswa }}</dd></div>
            <div><dt>Jenis</dt><dd>{{ $transaction->jenis_trans->label() }}</dd></div>
            <div class="receipt-total"><dt>Nominal</dt><dd>{{ \App\Support\Money::format($transaction->nominal) }}</dd></div>
            <div><dt>Saldo Setelah Transaksi</dt><dd>{{ \App\Support\Money::format($balanceAfter) }}</dd></div>
            <div><dt>Teller</dt><dd>{{ $transaction->teller->nama_petugas }}</dd></div>
        </dl>
        <p class="receipt-note">Struk ini dibuat otomatis oleh sistem. Simpan sebagai bukti transaksi.</p>
        <button class="button button-primary print-hidden" type="button" data-print-button>Cetak Struk</button>
    </main>
    <script src="{{ asset('js/print.js') }}" defer></script>
</body>
</html>
