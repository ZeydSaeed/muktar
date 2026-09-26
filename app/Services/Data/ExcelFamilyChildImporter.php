<?php

namespace App\Services\Data;

use App\Models\Family;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Support\FamilyRules;
use Illuminate\Support\Facades\Validator;

class ExcelFamilyChildImporter
{
    public function __construct(
        private ExcelFamilyMemberMapper $mapper,
        private ExcelRowNormalizer $normalizer,
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @param  list<array{row: int, values: array<string, mixed>}>  $rows
     * @param  list<string>  $errors
     */
    public function store(Family $family, array $rows, array &$errors): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if ($this->storeOne($family, $row, $errors)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param  array{row: int, values: array<string, mixed>}  $row
     * @param  list<string>  $errors
     */
    private function storeOne(Family $family, array $row, array &$errors): bool
    {
        $values = $this->normalizer->child($this->mapper->child($row['values']));

        if (trim((string) ($values['name'] ?? '')) === '') {
            $errors[] = "صف {$row['row']} (ابن): أدخل اسم الابن في عمود «اسم رب الاسرة».";

            return false;
        }

        $validator = Validator::make($values, FamilyRules::child());

        if ($validator->fails()) {
            $errors[] = "صف {$row['row']} (ابن): {$validator->errors()->first()}";

            return false;
        }

        $this->children->create($family, $validator->validated());

        return true;
    }
}
