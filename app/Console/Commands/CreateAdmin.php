<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email} {--name= : Administrator display name}';

    protected $description = 'Create an administrator securely, or grant administrator access to an existing user';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $validator = Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $user = User::where('email', $email)->first();
        if ($user === null) {
            if (! $this->input->isInteractive()) {
                $this->error('Run interactively to securely enter a password for a new administrator.');

                return self::FAILURE;
            }
            $password = $this->secret('Password (at least 12 characters)');
            $validator = Validator::make(['password' => $password], ['password' => ['required', 'string', 'min:12']]);
            if ($validator->fails()) {
                $this->error($validator->errors()->first());

                return self::FAILURE;
            }
            $user = new User;
            $user->name = $this->option('name') ?: 'Administrator';
            $user->email = $email;
            $user->password = $password;
        }
        $user->is_admin = true;
        $user->save();
        $this->info('Administrator access is ready.');

        return self::SUCCESS;
    }
}
