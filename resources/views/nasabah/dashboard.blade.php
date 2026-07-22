@extends('layouts.nasabah')

@section('title', 'Dashboard')

@section('content')
    <header class="portal-heading">
        <div><span class="eyebrow">Portal Nasabah</span><h1>Ringkasan rekening</h1><p>Pantau saldo dan transaksi rekening sekolah Anda.</p></div>
        <span class="system-badge"><span class="status-dot" aria-hidden="true"></span>Rekening aktif</span>
    </header>

    <section class="account-hero">
        <div>
            <span class="eyebrow">Saldo Tersedia</span>
            <h1>{{ \App\Support\Money::format($balance) }}</h1>
            <p>{{ $customer->nama_siswa }} · {{ \App\Support\AccountNumber::mask($customer->no_rekening) }}</p>
        </div>
        <div class="account-meta">
            <span>NIS <strong>{{ $customer->nis }}</strong></span>
            <span>Kelas <strong>{{ $customer->kelas }}</strong></span>
        </div>
    </section>

    <div class="safe-note"><strong>Periksa mutasi secara berkala.</strong><span>Segera laporkan kepada petugas apabila terdapat transaksi yang tidak dikenali.</span></div>

    <section class="content-card">
        <div class="section-heading">
            <div><span class="eyebrow">Mutasi Rekening</span><h2>Riwayat transaksi</h2></div>
            <form method="GET" action="{{ route('nasabah.dashboard') }}" class="filter-form">
                <label><span>Dari</span><input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}"></label>
                <label><span>Sampai</span><input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}"></label>
                <button class="button button-secondary" type="submit">Terapkan</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <caption class="visually-hidden">Riwayat transaksi rekening nasabah</caption>
                <thead><tr><th>Tanggal</th><th>Jenis</th><th>Teller</th><th class="numeric">Nominal</th></tr></thead>
                <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->tanggal->format('d M Y H:i') }}</td>
                        <td><span class="status {{ $transaction->jenis_trans === \App\Enums\TransactionType::DEPOSIT ? 'status-success' : 'status-warning' }}">{{ $transaction->jenis_trans->label() }}</span></td>
                        <td>{{ $transaction->teller->nama_petugas }}</td>
                        <td class="numeric {{ $transaction->jenis_trans === \App\Enums\TransactionType::DEPOSIT ? 'money-in' : 'money-out' }}">
                            {{ $transaction->jenis_trans === \App\Enums\TransactionType::DEPOSIT ? '+' : '-' }}{{ \App\Support\Money::format($transaction->nominal) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Belum ada transaksi pada rentang tanggal ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $transactions->links() }}</div>
    </section>
@endsection
