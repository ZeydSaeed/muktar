<?php

namespace Tests\Unit;

use Database\Seeders\Builders\FamilyDirectoryBuilder;
use Database\Seeders\Data\FamilyDirectoryCatalog;
use PHPUnit\Framework\TestCase;

class FamilyDirectoryBuilderTest extends TestCase
{
    public function test_catalog_lists_have_one_hundred_unique_entries(): void
    {
        $this->assertCount(100, array_unique(FamilyDirectoryCatalog::surnames()));
        $this->assertCount(100, array_unique(FamilyDirectoryCatalog::professions()));
        $this->assertCount(100, array_unique(FamilyDirectoryCatalog::birthPlaces()));
    }

    public function test_builder_creates_one_hundred_complete_unique_families(): void
    {
        $rows = (new FamilyDirectoryBuilder)->build();

        $this->assertCount(100, $rows);

        foreach ($rows as $row) {
            $this->assertNotSame('', $row['family']['head_of_family_name']);
            $this->assertGreaterThanOrEqual(2, count($row['children']));
        }

        $heads = array_map(
            static fn (array $row): string => $row['family']['head_of_family_name'],
            $rows,
        );
        $this->assertCount(100, array_unique($heads));
    }
}
