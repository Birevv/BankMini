<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && $actor->role === UserRole::ADMIN;
    }

    public function view(User $actor, User $user): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return $this->viewAny($actor);
    }

    public function update(User $actor, User $user): bool
    {
        return $this->viewAny($actor);
    }

    public function delete(User $actor, User $user): bool
    {
        return $this->viewAny($actor)
            && (! $actor->is($user))
            && (! $user->transactions()->exists())
            && (! $user->dailyReports()->exists())
            && (! $user->approvedReports()->exists());
    }

    public function deleteAny(User $actor): bool
    {
        return false;
    }

    public function restore(User $actor, User $user): bool
    {
        return false;
    }

    public function restoreAny(User $actor): bool
    {
        return false;
    }

    public function forceDelete(User $actor, User $user): bool
    {
        return false;
    }

    public function forceDeleteAny(User $actor): bool
    {
        return false;
    }

    public function replicate(User $actor, User $user): bool
    {
        return false;
    }

    public function reorder(User $actor): bool
    {
        return false;
    }
}
