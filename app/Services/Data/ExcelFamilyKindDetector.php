<?php

namespace App\Services\Data;

use App\Support\ExcelFamilyKind;

class ExcelFamilyKindDetector
{
    private const WIDOW_MARKER = 'ارمله';

    private const DECEASED_WIFE_MARKER = 'الزوجهمتوفاه';

    /**
     * @param  array<string, mixed>  $values
     */
    public function detect(array $values): ExcelFamilyKind
    {
        $text = $this->normalize((string) ($values['spouse_name'] ?? ''));

        if ($text === '') {
            return ExcelFamilyKind::Couple;
        }

        if ($text === self::WIDOW_MARKER) {
            return ExcelFamilyKind::Widow;
        }

        if ($text === self::DECEASED_WIFE_MARKER) {
            return ExcelFamilyKind::Widower;
        }

        return ExcelFamilyKind::Couple;
    }

    private function normalize(string $text): string
    {
        $text = strtr($text, [
            'أ' => 'ا',
            'إ' => 'ا',
            'آ' => 'ا',
            'ة' => 'ه',
            'ى' => 'ي',
            'ؤ' => 'و',
            'ئ' => 'ي',
            'ـ' => '',
        ]);

        return preg_replace('/\s+/u', '', $text) ?? $text;
    }
}
