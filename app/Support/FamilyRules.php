<?php

namespace App\Support;

final class FamilyRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function family(): array
    {
        return [
            'head_of_family_name' => ['required', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'integer', 'min:0'],
            'ration_card_number' => ['nullable', 'integer', 'min:0'],
            'housing_card_number' => ['nullable', 'integer', 'min:0'],
            'national_id_number' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:255'],
            'current_residence_date' => ['nullable', 'date'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'family_members_count' => ['nullable', 'integer', 'min:0'],
            'males_count' => ['nullable', 'integer', 'min:0'],
            'females_count' => ['nullable', 'integer', 'min:0'],
            'ration_agent_name' => ['nullable', 'string', 'max:255'],
            'housing_card_date' => ['nullable', 'date'],
            'issuing_authority' => ['nullable', 'string', 'max:255'],
            'current_governorate' => ['nullable', 'string', 'max:255'],
            'current_district' => ['nullable', 'string', 'max:255'],
            'current_subdistrict' => ['nullable', 'string', 'max:255'],
            'current_neighborhood' => ['nullable', 'string', 'max:255'],
            'current_alley' => ['nullable', 'string', 'max:255'],
            'previous_address' => ['nullable', 'string', 'max:255'],
            'previous_governorate' => ['nullable', 'string', 'max:255'],
            'previous_district' => ['nullable', 'string', 'max:255'],
            'previous_subdistrict' => ['nullable', 'string', 'max:255'],
            'previous_neighborhood' => ['nullable', 'string', 'max:255'],
            'previous_landmark' => ['nullable', 'string', 'max:255'],
            'previous_mukhtar_name' => ['nullable', 'string', 'max:255'],
            'karbala_residence_date' => ['nullable', 'date'],
            'registry_page_number' => ['nullable', 'integer', 'min:0'],
            'nearest_landmark' => ['nullable', 'string', 'max:255'],
            'secondary_mobile_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function child(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'national_id_number' => ['nullable', 'string', 'max:255'],
            'registry_number' => ['nullable', 'integer', 'min:0'],
            'page_number' => ['nullable', 'integer', 'min:0'],
            'profession' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function integerFamilyFields(): array
    {
        return [
            'house_number',
            'ration_card_number',
            'housing_card_number',
            'family_members_count',
            'males_count',
            'females_count',
            'registry_page_number',
        ];
    }

    /**
     * @return list<string>
     */
    public static function dateFamilyFields(): array
    {
        return [
            'current_residence_date',
            'birth_date',
            'karbala_residence_date',
            'housing_card_date',
        ];
    }

    /**
     * @return list<string>
     */
    public static function integerChildFields(): array
    {
        return ['registry_number', 'page_number'];
    }
}
