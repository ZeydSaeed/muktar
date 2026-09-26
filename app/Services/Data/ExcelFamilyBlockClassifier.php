<?php

namespace App\Services\Data;

use App\Support\ExcelFamilyKind;

class ExcelFamilyBlockClassifier
{
    public function __construct(
        private ExcelFamilyKindDetector $detector,
    ) {}

    /**
     * @param  list<array{row: int, values: array<string, mixed>}>  $members
     * @return array{
     *     kind: ExcelFamilyKind,
     *     startRow: int,
     *     head: array{row: int, values: array<string, mixed>},
     *     wife: array{row: int, values: array<string, mixed>}|null,
     *     children: list<array{row: int, values: array<string, mixed>}>
     * }
     */
    public function classify(array $members): array
    {
        $head = $members[0];
        $kind = $this->detector->detect($head['values']);

        if ($kind === ExcelFamilyKind::Couple) {
            return [
                'kind' => $kind,
                'startRow' => $head['row'],
                'head' => $head,
                'wife' => $members[1] ?? null,
                'children' => array_slice($members, 2),
            ];
        }

        return [
            'kind' => $kind,
            'startRow' => $head['row'],
            'head' => $head,
            'wife' => null,
            'children' => array_slice($members, 1),
        ];
    }
}
