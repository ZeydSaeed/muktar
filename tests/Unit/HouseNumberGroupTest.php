<?php

namespace Tests\Unit;

use App\Support\HouseNumberGroup;
use PHPUnit\Framework\TestCase;

class HouseNumberGroupTest extends TestCase
{
    public function test_group_uses_thousands_and_ten_thousands_digits(): void
    {
        $this->assertSame(31, HouseNumberGroup::of(31798));
        $this->assertSame(31, HouseNumberGroup::of(31000));
        $this->assertSame(31, HouseNumberGroup::of(31999));
        $this->assertSame(31, HouseNumberGroup::of(131798));
        $this->assertSame(32, HouseNumberGroup::of(32700));
        $this->assertSame(0, HouseNumberGroup::of(798));
    }

    public function test_label_is_two_digits(): void
    {
        $this->assertSame('31', HouseNumberGroup::label(31));
        $this->assertSame('06', HouseNumberGroup::label(6));
    }
}
