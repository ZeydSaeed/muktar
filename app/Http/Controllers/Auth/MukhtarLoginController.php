<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\EnterMukhtarAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EnterMukhtarRequest;
use Illuminate\Http\RedirectResponse;

class MukhtarLoginController extends Controller
{
    public function enter(
        EnterMukhtarRequest $request,
        EnterMukhtarAction $action,
    ): RedirectResponse {
        $action->execute($request->pin());

        return redirect()->intended(route('dashboard'));
    }
}
