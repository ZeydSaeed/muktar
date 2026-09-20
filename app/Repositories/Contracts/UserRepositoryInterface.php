<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findOrCreateMukhtar(): User;

    public function updatePin(User $user, string $pin): void;

    public function delete(User $user): void;
}
