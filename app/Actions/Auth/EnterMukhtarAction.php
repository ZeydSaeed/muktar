<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EnterMukhtarAction
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(string $pin): User
    {
        $user = $this->users->findOrCreateMukhtar();

        if (! Hash::check($pin, (string) $user->password)) {
            throw ValidationException::withMessages([
                'pin' => 'الرقم السري غير صحيح.',
            ]);
        }

        Auth::login($user, remember: true);

        return $user;
    }
}
