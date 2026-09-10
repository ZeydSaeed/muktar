<?php

namespace App\Actions\Settings;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteProfileAction
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(User $user, Request $request): void
    {
        Auth::logout();

        $this->users->delete($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
