<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('auth.admin_password');
        $hasConfiguredPassword = is_string($password) && mb_strlen(trim($password)) >= 12;
        $user = User::where('email', 'admin@example.com')->first();

        if ($user === null) {
            if (! $hasConfiguredPassword) {
                throw new RuntimeException('Set ADMIN_PASSWORD to at least 12 characters before seeding a new administrator.');
            }

            $user = new User;
            $user->name = 'Administrator';
            $user->email = 'admin@example.com';
            $user->password = bcrypt($password);
            $user->status = 1;
            $user->created_at = now();
            $user->created_by = Auth::check() ? Auth::user()->id : null;
        }

        if ($user !== null && ! $user->is_admin) {
            $user->updated_at = now();
            $user->updated_by = Auth::check() ? Auth::user()->id : null;
        }

        $user->is_admin = true;
        $user->save();
    }
}
