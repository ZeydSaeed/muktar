<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DeleteProfileAction;
use App\Actions\Settings\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function update(
        ProfileUpdateRequest $request,
        UpdateProfileAction $action,
    ): RedirectResponse {
        $action->execute($request->user(), $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile updated.')]);

        return to_route('profile.edit');
    }

    public function destroy(
        ProfileDeleteRequest $request,
        DeleteProfileAction $action,
    ): RedirectResponse {
        $action->execute($request->user(), $request);

        return redirect('/');
    }
}
