<?php

namespace App\Services\Data;

use App\Support\FamilyRules;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ExcelRowNormalizer
{
    /**
     * @var list<string>
     */
    private const TEXT_FIELDS = [
        'national_id_number',
        'mobile_number',
        'secondary_mobile_number',
    ];

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

            if (in_array($field, self::TEXT_FIELDS, true)) {
                $values[$field] = $this->textValue($value);

                continue;
            }

            if (in_array($field, $integers, true)) {
                $values[$field] = $this->integerValue($value);
            }

            if (in_array($field, $dates, true)) {
                $place = null;
                $values[$field] = $this->dateValue($value, $place);

                if ($field === 'birth_date' && $place !== null && empty($values['birth_place'])) {
                    $values['birth_place'] = $place;
                }
            }
        }

        return $values;
    }

    private function integerValue(mixed $value): ?int
    {
        if (is_int($value) || is_float($value)) {
            return (int) $value;
        }

        if (! is_string($value) || ! preg_match('/(\d+)/', $value, $match)) {
            return null;
        }

        return (int) $match[1];
    }

    private function dateValue(mixed $value, ?string &$place): ?string
    {
        if (is_numeric($value)) {
            return $this->numericDate((float) $value);
        }

        if (! is_string($value)) {
            return null;
        }

        $text = trim(str_replace('\\', '', $value));

        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $text, $match)) {
            return sprintf('%04d-%02d-%02d', $match[1], $match[2], $match[3]);
        }

        if (preg_match('/^(.+?)\s+(\d{4})$/u', $text, $match)) {
            $place = trim($match[1]);

            return $match[2].'-01-01';
        }

        if (preg_match('/^(\d{4})$/', $text)) {
            return $text.'-01-01';
        }

        return null;
    }

    private function textValue(mixed $value): ?string
    {
        if (is_int($value) || is_float($value)) {
            return sprintf('%.0f', $value);
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private function numericDate(float $value): ?string
    {
        $year = (int) $value;

        if ($year >= 1900 && $year <= 2100 && $value == $year) {
            return sprintf('%d-01-01', $year);
        }

        return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
    }
}
