<?php

namespace App\Http\Controllers\Data;

use App\Actions\Data\ExportFamilyBackupAction;
use App\Http\Controllers\Controller;
use App\Services\Data\FamilyBackupService;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function show(FamilyBackupService $backup): Response
    {
        return Inertia::render('data/backup', $backup->stats());
    }

    public function download(ExportFamilyBackupAction $action): StreamedResponse
    {
        $file = $action->execute();

        return response()->streamDownload(function () use ($file): void {
            echo $file['contents'];
        }, $file['filename'], ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
