<?php

namespace App\Models;

use App\Enums\JournalPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class JurnalAkuntansi extends Model
{
    protected $table = 'jurnal_akuntansi';

    /** @var list<string> */
    protected $fillable = [
        'id_transaksi',
        'kode_akun',
        'posisi',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'posisi' => JournalPosition::class,
            'jumlah' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Jurnal akuntansi tidak dapat diubah.'));
        static::deleting(fn (): never => throw new LogicException('Jurnal akuntansi tidak dapat dihapus.'));
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
