<?php

namespace App\Support;

final class HouseNumberGroup
{
    public const THOUSANDS_DIVISOR = 1000;

    public const TWO_DIGIT_MODULUS = 100;

    public static function of(int $houseNumber): int
    {
        return intdiv($houseNumber, self::THOUSANDS_DIVISOR) % self::TWO_DIGIT_MODULUS;
    }

    public static function label(int $group): string
    {
        return str_pad((string) $group, 2, '0', STR_PAD_LEFT);
    }

    public static function sqlExpression(): string
    {
        $divisor = self::THOUSANDS_DIVISOR;
        $modulus = self::TWO_DIGIT_MODULUS;

        return "(house_number - (house_number % {$divisor})) / {$divisor} % {$modulus}";
    }
}
