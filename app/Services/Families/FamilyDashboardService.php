<?php

namespace App\Services\Families;

use App\Data\FamilyTableFilters;
use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FamilyDashboardService
{
    public function __construct(
        private FamilyRepositoryInterface $families,
    ) {}

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    public function paginateForTable(
        ?string $search = null,
        ?FamilyTableFilters $filters = null,
    ): LengthAwarePaginator
    {
        $perPage = max(1, (int) config('mukhtar.pagination.families_per_page'));

        return $this->families
            ->paginateForDashboard($perPage, $search, $filters)
            ->through(fn (Family $family): array => $this->toTableRow($family));
    }

    /**
     * @return array<string, mixed>
     */
    private function toTableRow(Family $family): array
    {
        return [
            'id' => $family->id,
            'head_of_family_name' => $family->head_of_family_name,
            'profession' => $family->profession,
            'house_number' => $family->house_number,
            'mobile_number' => $family->mobile_number,
            'national_id_number' => $family->national_id_number,
            'birth_date' => $family->birth_date?->format('Y-m-d'),
            'children_count' => (int) $family->children_count,
        ];
    }
}
