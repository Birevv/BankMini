<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Transaksi;
use App\Models\User;

class TransaksiPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && in_array($actor->role, UserRole::cases(), true);
    }

    public function view(User $actor, Transaksi $transaction): bool
    {
        return $this->viewAny($actor)
            && ($actor->role !== UserRole::TELLER || $transaction->id_user === $actor->getKey());
    }

    public function create(User $actor): bool
    {
        return $actor->is_active && $actor->role === UserRole::TELLER;
    }

    public function update(User $actor, Transaksi $transaction): bool
    {
        return false;
    }

    public function delete(User $actor, Transaksi $transaction): bool
    {
        return false;
    }

    public function deleteAny(User $actor): bool
    {
        return false;
    }

    public function restore(User $actor, Transaksi $transaction): bool
    {
        return false;
    }

    public function restoreAny(User $actor): bool
    {
        return false;
    }

    public function forceDelete(User $actor, Transaksi $transaction): bool
    {
        return false;
    }

    public function forceDeleteAny(User $actor): bool
    {
        return false;
    }

    public function replicate(User $actor, Transaksi $transaction): bool
    {
        return false;
    }

    public function reorder(User $actor): bool
    {
        return false;
    }
}
