<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesktopUserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        User::query()->create([
            'name' => config('mukhtar.user.name'),
            'email' => config('mukhtar.user.email'),
            'password' => config('mukhtar.user.pin'),
        ]);
    }
}
