<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdatePasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Services\Settings\SecuritySettingsService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SecurityController extends Controller
{
    public function edit(
        TwoFactorAuthenticationRequest $request,
        SecuritySettingsService $securitySettings,
    ): Response {
        return Inertia::render(
            'settings/security',
            $securitySettings->buildPageProps($request),
        );
    }

    public function update(
        PasswordUpdateRequest $request,
        UpdatePasswordAction $action,
    ): RedirectResponse {
        $action->execute($request->user(), $request->password);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Password updated.')]);

        return back();
    }
}
