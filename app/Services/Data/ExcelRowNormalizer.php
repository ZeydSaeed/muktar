<?php

namespace App\Services\Data;

use App\Support\FamilyRules;

class ExcelRowNormalizer
{
    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function family(array $values): array
    {
        return $this->normalize(
            $values,
            FamilyRules::integerFamilyFields(),
            FamilyRules::dateFamilyFields(),
        );
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function child(array $values): array
    {
        return $this->normalize(
            $values,
            FamilyRules::integerChildFields(),
            ['birth_date'],
        );
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $integers
     * @param  list<string>  $dates
     * @return array<string, mixed>
     */
    private function normalize(array $values, array $integers, array $dates): array
    {
        foreach ($values as $field => $value) {
            if ($value === '' || $value === null) {
                $values[$field] = null;
                continue;
            }

            if (in_array($field, $integers, true)) {
                $values[$field] = (int) $value;
            }

            if (in_array($field, $dates, true) && is_numeric($value)) {
                $excelEpoch = (int) $value;
                $values[$field] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelEpoch)
                    ->format('Y-m-d');
            }
        }

        return $values;
    }
}
