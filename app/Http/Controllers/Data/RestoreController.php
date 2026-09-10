<?php

namespace App\Http\Controllers\Data;

use App\Actions\Data\RestoreFamilyBackupAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Data\RestoreBackupRequest;
use App\Services\Data\FamilyBackupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RestoreController extends Controller
{
    public function show(FamilyBackupService $backup): Response
    {
        return Inertia::render('data/restore', $backup->stats());
    }

    public function store(
        RestoreBackupRequest $request,
        RestoreFamilyBackupAction $action,
    ): RedirectResponse {
        $count = $action->execute($request->uploadedFile());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "تم استرداد {$count} عائلة.",
        ]);

        return redirect()->route('dashboard');
    }
}
