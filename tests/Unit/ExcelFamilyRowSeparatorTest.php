<?php

namespace Tests\Unit;

use App\Services\Data\ExcelFamilyRowSeparator;
use PHPUnit\Framework\TestCase;

class ExcelFamilyRowSeparatorTest extends TestCase
{
    public function test_empty_name_is_a_separator(): void
    {
        $this->assertTrue($this->isSeparator([]));
        $this->assertTrue($this->isSeparator(['head_of_family_name' => '']));
        $this->assertTrue($this->isSeparator(['head_of_family_name' => null, 'notes' => 'ع']));
    }

    public function test_dot_or_dash_name_is_a_separator(): void
    {
        $this->assertTrue($this->isSeparator(['head_of_family_name' => '.']));
        $this->assertTrue($this->isSeparator(['head_of_family_name' => '...']));
        $this->assertTrue($this->isSeparator(['head_of_family_name' => '-']));
    }

    public function test_person_name_is_not_a_separator(): void
    {
        $this->assertFalse($this->isSeparator(['head_of_family_name' => 'ضمياء شاكر']));
        $this->assertFalse($this->isSeparator(['head_of_family_name' => 'جواد حسن']));
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function isSeparator(array $values): bool
    {
        return (new ExcelFamilyRowSeparator)->matches($values);
    }
}
