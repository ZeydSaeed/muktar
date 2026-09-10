<?php

namespace App\Services\Families;

use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FamilyChildrenService
{
    public function __construct(
        private FamilyRepositoryInterface $families,
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @return array<string, mixed>|null
     */
    public function modalDataByFamilyId(?int $familyId): ?array
    {
        if ($familyId === null || $familyId < 1) {
            return null;
        }

        try {
            $family = $this->families->findById($familyId);
        } catch (ModelNotFoundException) {
            return null;
        }

        return [
            'family_id' => $family->id,
            'family_name' => $family->head_of_family_name,
            'children' => $this->children
                ->listForFamily($family)
                ->map(fn (FamilyChild $child): array => $this->toRow($child))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toRow(FamilyChild $child): array
    {
        return [
            'id' => $child->id,
            'name' => $child->name,
            'birth_date' => $child->birth_date?->format('Y-m-d'),
            'national_id_number' => $child->national_id_number,
            'registry_number' => $child->registry_number,
            'page_number' => $child->page_number,
            'profession' => $child->profession,
        ];
    }
}
