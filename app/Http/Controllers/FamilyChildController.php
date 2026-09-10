<?php

namespace App\Http\Controllers;

use App\Actions\Families\DeleteFamilyChildAction;
use App\Actions\Families\StoreFamilyChildAction;
use App\Actions\Families\UpdateFamilyChildAction;
use App\Http\Requests\Families\StoreFamilyChildRequest;
use App\Http\Requests\Families\UpdateFamilyChildRequest;
use App\Models\Family;
use App\Models\FamilyChild;
use App\Support\ChildrenModalRedirect;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class FamilyChildController extends Controller
{
    public function store(
        StoreFamilyChildRequest $request,
        Family $family,
        StoreFamilyChildAction $action,
    ): RedirectResponse {
        $action->execute($family, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم إضافة الابن بنجاح.',
        ]);

        return ChildrenModalRedirect::backTo($family->id);
    }

    public function update(
        UpdateFamilyChildRequest $request,
        Family $family,
        FamilyChild $familyChild,
        UpdateFamilyChildAction $action,
    ): RedirectResponse {
        abort_unless($familyChild->family_id === $family->id, 404);

        $action->execute($familyChild, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم تعديل بيانات الابن بنجاح.',
        ]);

        return ChildrenModalRedirect::backTo($family->id);
    }

    public function destroy(
        Family $family,
        FamilyChild $familyChild,
        DeleteFamilyChildAction $action,
    ): RedirectResponse {
        abort_unless($familyChild->family_id === $family->id, 404);

        $action->execute($familyChild);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'تم حذف الابن بنجاح.',
        ]);

        return ChildrenModalRedirect::backTo($family->id);
    }
}
