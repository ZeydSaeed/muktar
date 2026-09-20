<?php

namespace App\Support;

final class MukhtarPin
{
    public static function length(): int
    {
        return max(4, (int) config('mukhtar.pin.length', 6));
    }

    public static function default(): string
    {
        return (string) config('mukhtar.user.pin');
    }

    /**
     * @return list<string>
     */
    public static function rules(bool $confirmed = false): array
    {
        $rules = ['required', 'string', 'digits:'.self::length()];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
