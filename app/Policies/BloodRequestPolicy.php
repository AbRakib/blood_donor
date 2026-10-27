<?php

namespace App\Policies;

use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, BloodRequest $record): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, BloodRequest $record): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, BloodRequest $record): bool
    {
        return (bool) $user->is_admin;
    }
}
