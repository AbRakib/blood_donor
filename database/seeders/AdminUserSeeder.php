<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@gmail.com')->first();

        if ($user === null) {

            $user = new User;
            $user->name = 'Administrator';
            $user->email = 'admin@gmail.com';
            $user->password = bcrypt('123456');
            $user->status = 1;
            $user->created_at = now();
            $user->created_by = Auth::check() ? Auth::user()->id : null;
        } else {
            $user->updated_at = now();
            $user->updated_by = Auth::check() ? Auth::user()->id : null;
        }

        $user->is_admin = true;
        $user->save();
    }
}
