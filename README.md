# Bank Mini Sekolah

Sistem Informasi E-Teller Bank Mini Sekolah berbasis Laravel 13, Filament 5, Livewire 4, dan Tailwind CSS 4. Aplikasi menggunakan ledger transaksi sebagai sumber saldo, jurnal double-entry otomatis, serta pemisahan akses Administrator, Teller, Supervisor, dan Nasabah.

## Fitur

- Panel Administrator di `/admin` untuk petugas, nasabah, audit transaksi, jurnal, QR rekening, dan audit log.
- Panel Teller di `/teller` untuk pencarian/scan QR, setoran, penarikan dengan PIN, struk, riwayat pribadi, dan penutupan kas.
- Panel Supervisor di `/supervisor` untuk pemeriksaan transaksi/jurnal dan approval atau rejection laporan kas.
- Portal Nasabah di `/nasabah` dengan kata sandi terpisah untuk saldo dan mutasi rekening sendiri.
- Transaksi dan jurnal immutable, nominal Rupiah berupa integer, serta saldo minimum Rp10.000.
- Database transaction dan row lock untuk mencegah overdraft pada penarikan bersamaan.
- Audit trail tanpa password atau PIN mentah.

## Menjalankan Aplikasi

Persyaratan: PHP 8.3+, Composer, Node.js, NPM, serta MySQL 8+/MariaDB kompatibel.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Atur koneksi database pada `.env`, lalu jalankan:

```powershell
php artisan migrate --seed
npm install
npm run build
composer run dev
```

Seeder hanya untuk development dan otomatis tidak berjalan pada environment `production`.

## Akun Development

| Peran | Username / Rekening | Kata Sandi |
|---|---|---|
| Administrator | `admin` | `admin12345` |
| Teller | `teller` | `teller12345` |
| Supervisor | `supervisor` | `supervisor12345` |
| Nasabah | `BM-<tahun>-000001` | `nasabah123` |

PIN dummy penarikan Nasabah adalah `123456`. Ganti seluruh kredensial sebelum digunakan di luar development.

## Verifikasi

```powershell
php artisan test
vendor/bin/pint --test
npm run build
composer validate --strict
```

Pemindai QR memerlukan browser yang mendukung `BarcodeDetector` serta akses kamera melalui HTTPS atau `localhost`. Input nomor rekening manual selalu tersedia sebagai fallback.
