<?php

namespace App\Services\Families;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;

class FamilyRecordService
{
    public function __construct(
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toView(Family $family): array
    {
        return [
            'id' => $family->id,
            'title' => 'سجل عوائل حي الإسكان العسكري',
            'date' => $family->created_at?->format('d / m / Y'),
            'head' => [
                'full_name' => $family->head_of_family_name,
                'mother_name' => $family->mother_name,
                'birth_place' => $family->birth_place,
                'birth_date' => $family->birth_date?->format('Y-m-d'),
                'profession' => $family->profession,
                'ration_card_number' => $family->ration_card_number,
                'ration_agent_name' => $family->ration_agent_name,
                'housing_card_number' => $family->housing_card_number,
                'housing_card_date' => $family->housing_card_date?->format('Y-m-d'),
                'issuing_authority' => $family->issuing_authority,
                'current_governorate' => $family->current_governorate,
                'current_district' => $family->current_district,
                'current_subdistrict' => $family->current_subdistrict,
                'current_neighborhood' => $family->current_neighborhood,
                'current_alley' => $family->current_alley,
                'house_number' => $family->house_number,
                'nearest_landmark' => $family->nearest_landmark,
                'previous_governorate' => $family->previous_governorate,
                'previous_district' => $family->previous_district,
                'previous_subdistrict' => $family->previous_subdistrict,
                'previous_neighborhood' => $family->previous_neighborhood,
                'previous_address' => $family->previous_address,
                'previous_landmark' => $family->previous_landmark,
                'previous_mukhtar_name' => $family->previous_mukhtar_name,
                'karbala_residence_date' => $family->karbala_residence_date?->format('Y-m-d'),
                'mobile_number' => $family->mobile_number,
            ],
            'children' => $this->children
                ->listForFamily($family)
                ->map(fn (FamilyChild $child): array => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'birth_date' => $child->birth_date?->format('Y-m-d'),
                    'national_id_number' => $child->national_id_number,
                    'registry_number' => $child->registry_number,
                    'page_number' => $child->page_number,
                    'profession' => $child->profession,
                ])
                ->values()
                ->all(),
        ];
    }
}
