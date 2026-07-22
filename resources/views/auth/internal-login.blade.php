@extends('layouts.auth')

@section('title', 'Masuk Petugas')
@section('brand-role', 'Akses Internal')

@section('aside')
    <span class="eyebrow">Operasional sekolah</span>
    <h1>Operasional bank sekolah, lebih aman dan tertib.</h1>
    <p>Kelola transaksi, laporan kas, dan audit trail dalam satu sistem yang melindungi setiap langkah.</p>
    <ul class="security-list">
        <li>Ledger transaksi tidak dapat diubah.</li>
        <li>Jurnal double-entry otomatis.</li>
        <li>Akses terpisah sesuai role.</li>
    </ul>
@endsection

@section('content')
    <span class="eyebrow">Akses Petugas Internal</span>
    <h1>Masuk ke panel</h1>
    <p>Gunakan akun petugas aktif. Sistem akan mengarahkan Anda sesuai peran.</p>
    <form method="POST" action="{{ route('internal.login.store') }}" class="form-stack" data-safe-submit>
        @csrf
        <label>
            <span>Username</span>
            <input name="username" value="{{ old('username') }}" autocomplete="username" required autofocus placeholder="Masukkan username">
            <small>Gunakan akun Administrator, Teller, atau Supervisor.</small>
        </label>
        <label>
            <span>Kata Sandi</span>
            <input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan kata sandi">
            <small>Kata sandi tidak pernah ditampilkan kembali.</small>
        </label>
        <label class="check-label">
            <input type="checkbox" name="remember" value="1">
            <span>Ingat perangkat ini</span>
        </label>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif
        <button class="button button-primary button-block" type="submit">Masuk ke panel</button>
    </form>
    <div class="safe-note"><strong>Akses dicatat dalam audit trail.</strong><span>Jangan membagikan kredensial Anda.</span></div>
    <p class="auth-switch">Nasabah? <a href="{{ route('nasabah.login') }}">Masuk melalui Portal Nasabah</a></p>
@endsection
