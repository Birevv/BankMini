<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Harian {{ $report->tanggal->format('d-m-Y') }}</title>
    <link rel="stylesheet" href="{{ asset('css/bank-mini.css') }}">
</head>
<body class="print-page">
    <main class="report-sheet">
        <header>
            <div class="document-brand"><span class="brand-mark" aria-hidden="true">BM</span><span>{{ config('bank.name') }}</span></div>
            <span class="eyebrow">Laporan Penutupan Kas Teller</span>
            <h1>Ringkasan kas harian</h1>
            <p>{{ $report->tanggal->format('d M Y') }} · {{ $report->teller->nama_petugas }}</p>
        </header>
        <table>
            <tbody>
                <tr><th>Saldo Awal</th><td>{{ \App\Support\Money::format($report->saldo_awal) }}</td></tr>
                <tr><th>Total Setoran</th><td>{{ \App\Support\Money::format($report->total_setoran) }}</td></tr>
                <tr><th>Total Penarikan</th><td>{{ \App\Support\Money::format($report->total_penarikan) }}</td></tr>
                <tr><th>Saldo Akhir Sistem</th><td>{{ \App\Support\Money::format($report->saldo_akhir_sistem) }}</td></tr>
                <tr><th>Saldo Fisik</th><td>{{ \App\Support\Money::format($report->saldo_fisik) }}</td></tr>
                <tr><th>Selisih</th><td>{{ \App\Support\Money::format($report->selisih) }}</td></tr>
                <tr><th>Status</th><td>{{ $report->status->label() }}</td></tr>
                <tr><th>Supervisor</th><td>{{ $report->supervisor?->nama_petugas ?? '-' }}</td></tr>
            </tbody>
        </table>
        <p><strong>Catatan Teller:</strong> {{ $report->catatan_teller ?: '-' }}</p>
        <p><strong>Catatan Supervisor:</strong> {{ $report->catatan_supervisor ?: '-' }}</p>
        <button class="button button-primary print-hidden" type="button" data-print-button>Cetak Laporan</button>
    </main>
    <script src="{{ asset('js/print.js') }}" defer></script>
</body>
</html>
