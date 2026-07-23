<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table): void {
            $table->id();
            $table->string('no_rekening')->unique();
            $table->string('nis')->unique();
            $table->string('nama_siswa');
            $table->string('kelas');
            $table->string('pin_keamanan');
            $table->string('portal_password');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('transaksi', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_nasabah')->constrained('nasabah')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_user')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('tanggal')->index();
            $table->string('jenis_trans', 20)->index();
            $table->unsignedBigInteger('nominal');
            $table->timestamps();
            $table->index(['id_nasabah', 'tanggal']);
            $table->index(['id_user', 'tanggal']);
        });

        Schema::create('jurnal_akuntansi', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_transaksi')->constrained('transaksi')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('kode_akun', 20)->index();
            $table->string('posisi', 10)->index();
            $table->unsignedBigInteger('jumlah');
            $table->timestamps();

            $table->index(['id_transaksi', 'posisi']);
        });

        Schema::create('laporan_harian_teller', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_teller')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal');
            $table->unsignedBigInteger('saldo_awal')->default(0);
            $table->unsignedBigInteger('total_setoran')->default(0);
            $table->unsignedBigInteger('total_penarikan')->default(0);
            $table->unsignedBigInteger('saldo_akhir_sistem')->default(0);
            $table->unsignedBigInteger('saldo_fisik')->default(0);
            $table->bigInteger('selisih')->default(0);
            $table->string('status', 20)->index();
            $table->text('catatan_teller')->nullable();
            $table->text('catatan_supervisor')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['id_teller', 'tanggal']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE transaksi ADD CONSTRAINT transaksi_nominal_positive CHECK (nominal > 0)');
            DB::statement('ALTER TABLE jurnal_akuntansi ADD CONSTRAINT jurnal_jumlah_positive CHECK (jumlah > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_harian_teller');
        Schema::dropIfExists('jurnal_akuntansi');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('nasabah');
    }
};
