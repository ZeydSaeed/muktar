<?php

namespace App\Http\Controllers\Data;

use App\Actions\Data\ExportFamilyBackupAction;
use App\Actions\Data\SaveFamilyBackupAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Data\SaveBackupRequest;
use App\Services\Data\FamilyBackupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function show(FamilyBackupService $backup): Response
    {
        return Inertia::render('data/backup', $backup->pageData());
    }

    public function store(
        SaveBackupRequest $request,
        SaveFamilyBackupAction $action,
    ): RedirectResponse {
        $path = $action->execute($request->directory());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "تم حفظ النسخة الاحتياطية في {$path}",
        ]);

        return back();
    }

    public function download(ExportFamilyBackupAction $action): StreamedResponse
    {
        $file = $action->execute();

        return response()->streamDownload(function () use ($file): void {
            echo $file['contents'];
        }, $file['filename'], ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
