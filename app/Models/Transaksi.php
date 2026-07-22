<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    /** @var list<string> */
    protected $fillable = [
        'id_nasabah',
        'id_user',
        'tanggal',
        'jenis_trans',
        'nominal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'jenis_trans' => TransactionType::class,
            'nominal' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Ledger transaksi tidak dapat diubah.'));
        static::deleting(fn (): never => throw new LogicException('Ledger transaksi tidak dapat dihapus.'));
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah');
    }

    public function teller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(JurnalAkuntansi::class, 'id_transaksi');
    }

    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('id_nasabah', $customerId);
    }

    public function scopeForTeller(Builder $query, int $tellerId): Builder
    {
        return $query->where('id_user', $tellerId);
    }
}
