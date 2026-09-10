<?php

namespace App\Support;

final class HouseNumberGroup
{
    public static function of(int $houseNumber): int
    {
        return intdiv($houseNumber, 100);
    }

    /**
     * @return array{0: int, 1: int}
     */
    public static function range(int $group): array
    {
        return [$group * 100, $group * 100 + 99];
    }

    public static function label(int $group): string
    {
        [$from, $to] = self::range($group);

        return $group.' ('.$from.'–'.$to.')';
    }
}
