<?php

namespace Database\Seeders\Support;

use DomainException;

final class UniqueColumnGuard
{
    /**
     * @param  list<mixed>  $values
     */
    public function assert(string $column, array $values): void
    {
        $normalized = array_map(
            static fn (mixed $value): string => trim((string) $value),
            $values,
        );

        foreach ($normalized as $value) {
            if ($value === '') {
                throw new DomainException("Empty value in column [{$column}].");
            }
        }

        if (count($normalized) !== count(array_unique($normalized))) {
            throw new DomainException("Duplicate values found in column [{$column}].");
        }
    }
}
