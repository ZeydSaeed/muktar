<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\MukhtarPin;

class UserRepository implements UserRepositoryInterface
{
    public function findOrCreateMukhtar(): User
    {
        return User::firstOrCreate(
            ['email' => config('mukhtar.user.email')],
            [
                'name' => config('mukhtar.user.name'),
                'password' => MukhtarPin::default(),
                'email_verified_at' => now(),
            ],
        );
    }

    public function updatePin(User $user, string $pin): void
    {
        $user->update([
            'password' => $pin,
        ]);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
