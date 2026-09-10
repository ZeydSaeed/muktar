<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findOrCreateMukhtar(): User;

    public function delete(User $user): void;
}
