<?php

namespace App\Services\Data;

class ExcelFamilyMemberMapper
{
    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function father(array $values): array
    {
        return $values;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array{spouse_name: mixed, spouse_profession: mixed}
     */
    public function wife(array $values): array
    {
        return [
            'spouse_name' => $this->firstFilled($values, [
                'head_of_family_name',
                'spouse_name',
            ]),
            'spouse_profession' => $this->firstFilled($values, [
                'profession',
                'spouse_profession',
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function child(array $values): array
    {
        return [
            'name' => $values['head_of_family_name'] ?? null,
            'birth_date' => $values['birth_date'] ?? null,
            'national_id_number' => $values['national_id_number'] ?? null,
            'profession' => $values['profession'] ?? null,
            'page_number' => $values['registry_page_number'] ?? null,
            'registry_number' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $fields
     */
    private function firstFilled(array $values, array $fields): mixed
    {
        foreach ($fields as $field) {
            $value = $values[$field] ?? null;

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
