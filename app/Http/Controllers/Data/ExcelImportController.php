<?php

namespace App\Http\Controllers\Data;

use App\Actions\Data\ImportFamiliesFromExcelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Data\ImportExcelRequest;
use App\Services\Data\ExcelImportGuideService;
use App\Services\Data\ExcelTemplateService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelImportController extends Controller
{
    public function show(ExcelImportGuideService $guide): Response
    {
        return Inertia::render('data/excel-import', $guide->pageProps());
    }

    public function template(ExcelTemplateService $templates): StreamedResponse
    {
        return response()->streamDownload(function () use ($templates): void {
            echo $templates->contents();
        }, $templates->filename());
    }

    public function store(
        ImportExcelRequest $request,
        ImportFamiliesFromExcelAction $action,
    ): RedirectResponse {
        $result = $action->execute($request->uploadedFile());

        Inertia::flash('toast', [
            'type' => $result->toastType(),
            'message' => $result->toastMessage(),
        ]);

        return back();
    }
}
