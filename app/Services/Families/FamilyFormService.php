<?php

namespace App\Services\Families;

use App\Models\Family;

class FamilyFormService
{
    /**
     * @return array<string, mixed>
     */
    public function toForm(Family $family): array
    {
        return [
            'id' => $family->id,
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
        ];
    }
}
