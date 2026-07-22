<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Nasabah;
use App\Models\User;

class NasabahPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && in_array($actor->role, UserRole::cases(), true);
    }

    public function view(User $actor, Nasabah $nasabah): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return $actor->is_active && $actor->role === UserRole::ADMIN;
    }

    public function update(User $actor, Nasabah $nasabah): bool
    {
        return $this->create($actor);
    }

    public function delete(User $actor, Nasabah $nasabah): bool
    {
        return $this->create($actor) && (! $nasabah->transactions()->exists());
    }

    public function deleteAny(User $actor): bool
    {
        return false;
    }

    public function restore(User $actor, Nasabah $nasabah): bool
    {
        return false;
    }

    public function restoreAny(User $actor): bool
    {
        return false;
    }

    public function forceDelete(User $actor, Nasabah $nasabah): bool
    {
        return false;
    }

    public function forceDeleteAny(User $actor): bool
    {
        return false;
    }

    public function replicate(User $actor, Nasabah $nasabah): bool
    {
        return false;
    }

    public function reorder(User $actor): bool
    {
        return false;
    }
}
