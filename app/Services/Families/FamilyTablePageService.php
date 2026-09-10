<?php

namespace App\Services\Families;

use App\Http\Requests\Dashboard\DashboardRequest;

class FamilyTablePageService
{
    public function __construct(
        private FamilyDashboardService $table,
        private FamilyChildrenService $children,
        private FamilyFilterOptionsService $filterOptions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function props(DashboardRequest $request): array
    {
        $filters = $request->filters();

        return [
            'families' => $this->table->paginateForTable($request->searchTerm(), $filters),
            'search' => $request->searchTerm() ?? '',
            'filters' => $filters->toFrontend(),
            'filters_active' => $filters->isActive(),
            'filterOptions' => $this->filterOptions->forTable(),
            'childrenModal' => $this->children->modalDataByFamilyId(
                $request->integer('family_children') ?: null,
            ),
        ];
    }
}
