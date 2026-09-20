<?php

namespace App\Actions\Settings;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdatePinAction
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(User $user, string $currentPin, string $pin): void
    {
        if (! Hash::check($currentPin, (string) $user->password)) {
            throw ValidationException::withMessages([
                'current_pin' => 'الرقم السري الحالي غير صحيح.',
            ]);
        }

        $this->users->updatePin($user, $pin);
    }
}
