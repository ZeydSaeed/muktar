<?php

namespace Tests\Unit;

use App\Services\Data\ExcelFamilyKindDetector;
use App\Support\ExcelFamilyKind;
use PHPUnit\Framework\TestCase;

class ExcelFamilyKindDetectorTest extends TestCase
{
    public function test_detects_couple_when_spouse_name_is_a_person(): void
    {
        $this->assertSame(ExcelFamilyKind::Couple, $this->detect([
            'head_of_family_name' => 'حسن علي',
            'spouse_name' => 'فاطمة محمد',
            'profession' => 'أرملة',
        ]));
    }

    public function test_detects_widow_from_spouse_name_spellings(): void
    {
        $this->assertSame(ExcelFamilyKind::Widow, $this->detect(['spouse_name' => 'ارملة']));
        $this->assertSame(ExcelFamilyKind::Widow, $this->detect(['spouse_name' => 'ارمله']));
        $this->assertSame(ExcelFamilyKind::Widow, $this->detect(['spouse_name' => 'أرملة']));
    }

    public function test_detects_deceased_wife_from_spouse_name_spellings(): void
    {
        $this->assertSame(ExcelFamilyKind::Widower, $this->detect([
            'spouse_name' => 'الزوجة متوفاة',
        ]));
        $this->assertSame(ExcelFamilyKind::Widower, $this->detect([
            'spouse_name' => 'الزوجه متوفاه',
        ]));
        $this->assertSame(ExcelFamilyKind::Widower, $this->detect([
            'spouse_name' => 'الزوجة متوفاه',
        ]));
    }

    public function test_ignores_status_words_outside_spouse_name(): void
    {
        $this->assertSame(ExcelFamilyKind::Couple, $this->detect([
            'profession' => 'أرملة',
            'notes' => 'الزوجة متوفاة',
        ]));
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function detect(array $values): ExcelFamilyKind
    {
        return (new ExcelFamilyKindDetector)->detect($values);
    }
}
