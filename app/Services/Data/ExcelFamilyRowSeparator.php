<?php

namespace App\Services\Data;

class ExcelFamilyRowSeparator
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function matches(array $values): bool
    {
        $name = trim((string) ($values['head_of_family_name'] ?? ''));

        if ($name === '') {
            return true;
        }

        $letters = preg_replace('/[\s.\-_ـ،,\/\\\\*|]+/u', '', $name);

        return $letters === null || $letters === '';
    }
}
