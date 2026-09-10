<?php

namespace App\Services\Data;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use App\Support\FamilyRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FamilyBackupService
{
    public function __construct(
        private FamilyRepositoryInterface $families,
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @return array{families_count: int, children_count: int}
     */
    public function stats(): array
    {
        return [
            'families_count' => $this->families->count(),
            'children_count' => $this->children->count(),
        ];
    }

    public function filename(): string
    {
        return 'نسخ-احتياطي-العوائل-'.now()->format('Y-m-d').'.json';
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'schema' => (string) config('mukhtar.data.backup_schema'),
            'exported_at' => now()->toIso8601String(),
            'families' => $this->families
                ->allWithChildren()
                ->map(fn (Family $family): array => $this->familyPayload($family))
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function restore(array $payload): int
    {
        $this->assertValid($payload);

        return DB::transaction(function () use ($payload): int {
            $this->children->deleteAll();
            $this->families->deleteAll();
            $count = 0;

            foreach ($payload['families'] as $row) {
                $this->restoreFamily($row);
                $count++;
            }

            return $count;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function familyPayload(Family $family): array
    {
        return [
            'head_of_family_name' => $family->head_of_family_name,
            'spouse_name' => $family->spouse_name,
            'profession' => $family->profession,
            'mother_name' => $family->mother_name,
            'house_number' => $family->house_number,
            'ration_card_number' => $family->ration_card_number,
            'housing_card_number' => $family->housing_card_number,
            'national_id_number' => $family->national_id_number,
            'mobile_number' => $family->mobile_number,
            'current_residence_date' => $family->current_residence_date?->format('Y-m-d'),
            'birth_date' => $family->birth_date?->format('Y-m-d'),
            'birth_place' => $family->birth_place,
            'family_members_count' => $family->family_members_count,
            'males_count' => $family->males_count,
            'females_count' => $family->females_count,
            'ration_agent_name' => $family->ration_agent_name,
            'housing_card_date' => $family->housing_card_date?->format('Y-m-d'),
            'issuing_authority' => $family->issuing_authority,
            'current_governorate' => $family->current_governorate,
            'current_district' => $family->current_district,
            'current_subdistrict' => $family->current_subdistrict,
            'current_neighborhood' => $family->current_neighborhood,
            'current_alley' => $family->current_alley,
            'previous_address' => $family->previous_address,
            'previous_governorate' => $family->previous_governorate,
            'previous_district' => $family->previous_district,
            'previous_subdistrict' => $family->previous_subdistrict,
            'previous_neighborhood' => $family->previous_neighborhood,
            'previous_landmark' => $family->previous_landmark,
            'previous_mukhtar_name' => $family->previous_mukhtar_name,
            'karbala_residence_date' => $family->karbala_residence_date?->format('Y-m-d'),
            'registry_page_number' => $family->registry_page_number,
            'nearest_landmark' => $family->nearest_landmark,
            'secondary_mobile_number' => $family->secondary_mobile_number,
            'notes' => $family->notes,
            'children' => $family->children
                ->map(fn (FamilyChild $child): array => $this->childPayload($child))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function childPayload(FamilyChild $child): array
    {
        return [
            'name' => $child->name,
            'birth_date' => $child->birth_date?->format('Y-m-d'),
            'national_id_number' => $child->national_id_number,
            'registry_number' => $child->registry_number,
            'page_number' => $child->page_number,
            'profession' => $child->profession,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertValid(array $payload): void
    {
        $validator = Validator::make($payload, [
            'schema' => ['required', 'in:'.config('mukhtar.data.backup_schema')],
            'families' => ['required', 'array'],
            'families.*.head_of_family_name' => ['required', 'string', 'max:255'],
            'families.*.children' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function restoreFamily(array $row): void
    {
        $children = $row['children'] ?? [];
        unset($row['children']);
        $familyData = Validator::make($row, FamilyRules::family())->validate();
        $family = $this->families->create($familyData);

        foreach ($children as $child) {
            $childData = Validator::make($child, FamilyRules::child())->validate();
            $this->children->create($family, $childData);
        }
    }
}
