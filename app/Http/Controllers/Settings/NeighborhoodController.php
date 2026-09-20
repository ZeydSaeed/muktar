<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdateNeighborhoodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateNeighborhoodRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class NeighborhoodController extends Controller
{
    public function update(
        UpdateNeighborhoodRequest $request,
        UpdateNeighborhoodAction $action,
    ): RedirectResponse {
        $action->execute($request->neighborhood());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم حفظ اسم الحي',
        ]);

        return back();
    }
}
