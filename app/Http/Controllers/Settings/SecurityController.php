<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdatePinAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePinRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SecurityController extends Controller
{
    public function update(
        UpdatePinRequest $request,
        UpdatePinAction $action,
    ): RedirectResponse {
        $action->execute(
            $request->user(),
            $request->currentPin(),
            $request->pin(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم تغيير الرقم السري.',
        ]);

        return to_route('profile.edit');
    }
}
