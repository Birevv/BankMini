<?php

namespace App\Models;

use Database\Factories\NasabahFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LogicException;

class Nasabah extends Authenticatable
{
    /** @use HasFactory<NasabahFactory> */
    use HasFactory;

    protected $table = 'nasabah';

    /** @var list<string> */
    protected $fillable = [
        'no_rekening',
        'nis',
        'nama_siswa',
        'kelas',
        'pin_keamanan',
        'portal_password',
        'is_active',
    ];

    /** @var list<string> */
    protected $hidden = [
        'pin_keamanan',
        'portal_password',
    ];

    protected function casts(): array
    {
        return [
            'pin_keamanan' => 'hashed',
            'portal_password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $customer): void {
            if ($customer->isDirty('no_rekening') && $customer->transactions()->exists()) {
                throw new LogicException('Nomor rekening yang sudah memiliki transaksi tidak dapat diubah.');
            }
        });
    }

    public function getAuthPassword(): string
    {
        return $this->portal_password;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_nasabah');
    }
}
