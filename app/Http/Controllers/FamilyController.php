<?php

namespace App\Http\Controllers;

use App\Actions\Families\DeleteFamiliesAction;
use App\Actions\Families\DeleteFamilyAction;
use App\Actions\Families\StoreFamilyAction;
use App\Actions\Families\UpdateFamilyAction;
use App\Http\Requests\Dashboard\DashboardRequest;
use App\Http\Requests\Families\BulkDeleteFamiliesRequest;
use App\Http\Requests\Families\StoreFamilyRequest;
use App\Http\Requests\Families\UpdateFamilyRequest;
use App\Models\Family;
use App\Services\Families\FamilyFormService;
use App\Services\Families\FamilyRecordService;
use App\Services\Families\FamilyTablePageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FamilyController extends Controller
{
    public function index(
        DashboardRequest $request,
        FamilyTablePageService $page,
    ): Response {
        return Inertia::render('families/index', $page->props($request));
    }

    public function create(): Response
    {
        return Inertia::render('families/create');
    }

    public function store(
        StoreFamilyRequest $request,
        StoreFamilyAction $action,
    ): RedirectResponse {
        $action->execute($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم إضافة العائلة بنجاح.',
        ]);

        return redirect()->route('families.index');
    }

    public function edit(Family $family, FamilyFormService $form): Response
    {
        return Inertia::render('families/edit', [
            'family' => $form->toForm($family),
        ]);
    }

    public function record(Family $family, FamilyRecordService $records): Response
    {
        return Inertia::render('families/record', [
            'record' => $records->toView($family),
        ]);
    }

    public function update(
        UpdateFamilyRequest $request,
        Family $family,
        UpdateFamilyAction $action,
    ): RedirectResponse {
        $action->execute($family, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم تعديل العائلة بنجاح.',
        ]);

        return redirect()->route('families.index');
    }

    public function deletion(
        DashboardRequest $request,
        FamilyTablePageService $page,
    ): Response {
        return Inertia::render('families/deletion', $page->props($request));
    }

    public function destroy(
        Family $family,
        DeleteFamilyAction $action,
    ): RedirectResponse {
        $action->execute($family);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم حذف العائلة بنجاح.',
        ]);

        return back();
    }

    public function bulkDestroy(
        BulkDeleteFamiliesRequest $request,
        DeleteFamiliesAction $action,
    ): RedirectResponse {
        $count = $action->execute($request->ids());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $count === 1
                ? 'تم حذف العائلة بنجاح.'
                : 'تم حذف العوائل المحددة بنجاح.',
        ]);

        return back();
    }
}
