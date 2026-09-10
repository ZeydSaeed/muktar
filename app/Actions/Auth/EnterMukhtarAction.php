<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class EnterMukhtarAction
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(): User
    {
        $user = $this->users->findOrCreateMukhtar();

        Auth::login($user, remember: true);

        return $user;
    }
}
