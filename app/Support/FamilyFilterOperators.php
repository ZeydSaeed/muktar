<?php

namespace App\Support;

final class FamilyFilterOperators
{
    public const GREATER_THAN = 'gt';

    public const LESS_THAN = 'lt';

    public const BEFORE = 'before';

    public const AFTER = 'after';

    public const BETWEEN = 'between';

    /**
     * @return list<string>
     */
    public static function comparisons(): array
    {
        return [self::GREATER_THAN, self::LESS_THAN];
    }

    /**
     * @return list<string>
     */
    public static function dates(): array
    {
        return [self::BEFORE, self::AFTER, self::BETWEEN];
    }
}
