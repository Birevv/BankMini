@extends('layouts.auth')

@section('title', 'Portal Nasabah')
@section('brand-role', 'Portal Nasabah')

@section('aside')
    <span class="eyebrow">Tabungan sekolah</span>
    <h1>Pantau tabunganmu dengan tenang.</h1>
    <p>Lihat saldo dan riwayat transaksi rekening sekolahmu melalui akses yang terpisah dan terlindungi.</p>
    <ul class="security-list">
        <li>Hanya dapat melihat rekening sendiri.</li>
        <li>Nomor rekening ditampilkan secara aman.</li>
        <li>PIN penarikan tidak digunakan untuk login.</li>
    </ul>
@endsection

@section('content')
    <span class="eyebrow">Portal Nasabah</span>
    <h1>Masuk ke rekening</h1>
    <p>Gunakan nomor rekening dan kata sandi portal untuk melihat saldo serta mutasi.</p>
    <form method="POST" action="{{ route('nasabah.login.store') }}" class="form-stack" data-safe-submit>
        @csrf
        <label>
            <span>Nomor Rekening</span>
            <input name="no_rekening" value="{{ old('no_rekening') }}" autocomplete="username" required autofocus placeholder="BM-2026-000001">
            <small>Nomor rekening tersedia pada kartu nasabah atau QR.</small>
        </label>
        <label>
            <span>Kata Sandi Portal</span>
            <input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan kata sandi portal">
            <small>Jangan gunakan PIN enam digit pada halaman ini.</small>
        </label>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif
        <button class="button button-primary button-block" type="submit">Masuk dengan aman</button>
    </form>
    <div class="safe-note"><strong>PIN tidak pernah diminta di halaman ini.</strong><span>PIN hanya digunakan saat penarikan bersama Teller.</span></div>
    <p class="auth-switch">Petugas sekolah? <a href="{{ route('internal.login') }}">Masuk melalui Panel Petugas</a></p>
@endsection
