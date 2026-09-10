<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\EnterMukhtarAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MukhtarLoginController extends Controller
{
    public function enter(EnterMukhtarAction $action): RedirectResponse
    {
        $action->execute();

        return redirect()->intended(route('dashboard'));
    }
}
