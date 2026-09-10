<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function findOrCreateMukhtar(): User
    {
        return User::firstOrCreate(
            ['email' => config('mukhtar.user.email')],
            [
                'name' => config('mukhtar.user.name'),
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => now(),
            ],
        );
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
