<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'username',
        'password',
        'nama_petugas',
        'role',
        'is_active',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && match ($panel->getId()) {
            UserRole::ADMIN->value => $this->role === UserRole::ADMIN,
            UserRole::TELLER->value => $this->role === UserRole::TELLER,
            UserRole::SUPERVISOR->value => $this->role === UserRole::SUPERVISOR,
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        return $this->nama_petugas;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(LaporanHarianTeller::class, 'id_teller');
    }

    public function approvedReports(): HasMany
    {
        return $this->hasMany(LaporanHarianTeller::class, 'approved_by');
    }

    public function isRole(UserRole $role): bool
    {
        return $this->role === $role;
    }
}
