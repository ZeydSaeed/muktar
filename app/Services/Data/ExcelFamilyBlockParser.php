<?php

namespace App\Services\Data;

use App\Support\ExcelFamilyKind;

class ExcelFamilyBlockParser
{
    public function __construct(
        private ExcelFamilyBlockClassifier $classifier,
        private ExcelFamilyRowSeparator $separator,
    ) {}

    /**
     * @param  list<array{row: int, values: array<string, mixed>}>  $rows
     * @return list<array{
     *     kind: ExcelFamilyKind,
     *     startRow: int,
     *     head: array{row: int, values: array<string, mixed>},
     *     wife: array{row: int, values: array<string, mixed>}|null,
     *     children: list<array{row: int, values: array<string, mixed>}>
     * }>
     */
    public function parse(array $rows): array
    {
        $blocks = [];
        $current = [];

        foreach ($rows as $row) {
            if ($this->isEmpty($row['values'])) {
                $this->pushBlock($blocks, $current);
                $current = [];

                continue;
            }

            $current[] = $row;
        }

        $this->pushBlock($blocks, $current);

        return $blocks;
    }

    /**
     * @param  list<array{kind: ExcelFamilyKind, startRow: int, head: array{row: int, values: array<string, mixed>}, wife: array{row: int, values: array<string, mixed>}|null, children: list<array{row: int, values: array<string, mixed>}>}>  $blocks
     * @param  list<array{row: int, values: array<string, mixed>}>  $members
     */
    private function pushBlock(array &$blocks, array $members): void
    {
        if ($members === []) {
            return;
        }

        $blocks[] = $this->classifier->classify($members);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function isEmpty(array $values): bool
    {
        return $this->separator->matches($values);
    }
}
