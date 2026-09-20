<?php

use App\Support\MukhtarPin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $email = config('mukhtar.user.email');

        $user = DB::table('users')->where('email', $email)->first();

        if ($user === null) {
            return;
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make(MukhtarPin::default()),
        ]);
    }

    public function down(): void
    {
        //
    }
};
