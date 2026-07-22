<?php

namespace App\Models;

use App\Enums\DailyReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class LaporanHarianTeller extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian_teller';

    /** @var list<string> */
    protected $fillable = [
        'id_teller',
        'tanggal',
        'saldo_awal',
        'total_setoran',
        'total_penarikan',
        'saldo_akhir_sistem',
        'saldo_fisik',
        'selisih',
        'status',
        'catatan_teller',
        'catatan_supervisor',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'saldo_awal' => 'integer',
            'total_setoran' => 'integer',
            'total_penarikan' => 'integer',
            'saldo_akhir_sistem' => 'integer',
            'saldo_fisik' => 'integer',
            'selisih' => 'integer',
            'approved_at' => 'datetime',
            'status' => DailyReportStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $report): void {
            if ($report->getRawOriginal('status') === DailyReportStatus::APPROVED->value) {
                throw new LogicException('Laporan yang sudah disetujui tidak dapat diubah.');
            }
        });

        static::deleting(function (self $report): void {
            if ($report->status !== DailyReportStatus::DRAFT) {
                throw new LogicException('Laporan yang sudah diajukan tidak dapat dihapus.');
            }
        });
    }

    public function teller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_teller');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
