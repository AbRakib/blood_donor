<?php

namespace App\Policies;

use App\Models\BloodDonor;
use App\Models\User;

class BloodDonorPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, BloodDonor $record): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, BloodDonor $record): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, BloodDonor $record): bool
    {
        return (bool) $user->is_admin;
    }
}
