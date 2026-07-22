<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\JurnalAkuntansi;
use App\Models\User;

class JurnalAkuntansiPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && in_array($actor->role, [UserRole::ADMIN, UserRole::SUPERVISOR], true);
    }

    public function view(User $actor, JurnalAkuntansi $journal): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, JurnalAkuntansi $journal): bool
    {
        return false;
    }

    public function delete(User $actor, JurnalAkuntansi $journal): bool
    {
        return false;
    }

    public function deleteAny(User $actor): bool
    {
        return false;
    }

    public function restore(User $actor, JurnalAkuntansi $journal): bool
    {
        return false;
    }

    public function restoreAny(User $actor): bool
    {
        return false;
    }

    public function forceDelete(User $actor, JurnalAkuntansi $journal): bool
    {
        return false;
    }

    public function forceDeleteAny(User $actor): bool
    {
        return false;
    }

    public function replicate(User $actor, JurnalAkuntansi $journal): bool
    {
        return false;
    }

    public function reorder(User $actor): bool
    {
        return false;
    }
}
