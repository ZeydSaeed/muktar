<?php

namespace App\Services\Data;

use App\Data\ExcelImportResult;
use App\Models\Family;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use App\Support\FamilyExcelColumns;
use App\Support\FamilyRules;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportFamiliesFromExcelService
{
    public function __construct(
        private ExcelSheetReader $reader,
        private ExcelRowNormalizer $normalizer,
        private FamilyRepositoryInterface $families,
        private FamilyChildRepositoryInterface $children,
    ) {}

    public function import(UploadedFile $file): ExcelImportResult
    {
        $path = $file->getRealPath();

        if ($path === false) {
            return new ExcelImportResult(0, 0, ['تعذر قراءة الملف.']);
        }

        return DB::transaction(fn (): ExcelImportResult => $this->run($path));
    }

    private function run(string $path): ExcelImportResult
    {
        $errors = [];
        $index = [];
        $familyCount = $this->importFamilies($path, $index, $errors);
        $childCount = $this->importChildren($path, $index, $errors);

        return new ExcelImportResult($familyCount, $childCount, $errors);
    }

    /**
     * @param  array<string, Family>  $index
     * @param  list<string>  $errors
     */
    private function importFamilies(string $path, array &$index, array &$errors): int
    {
        $rows = $this->reader->mappedRows(
            $path,
            (string) config('mukhtar.data.families_sheet'),
            FamilyExcelColumns::familyHeaders(),
            true,
        );
        $count = 0;

        foreach ($rows as $row) {
            $values = $this->normalizer->family($row['values']);
            $validator = Validator::make($values, FamilyRules::family());

            if ($validator->fails()) {
                $errors[] = $this->rowError('العوائل', $row['row'], $validator->errors()->first());
                continue;
            }

            $family = $this->families->create($validator->validated());
            $this->remember($index, $family);
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<string, Family>  $index
     * @param  list<string>  $errors
     */
    private function importChildren(string $path, array $index, array &$errors): int
    {
        $rows = $this->reader->mappedRows(
            $path,
            (string) config('mukhtar.data.children_sheet'),
            FamilyExcelColumns::childHeaders(),
        );
        $count = 0;

        foreach ($rows as $row) {
            $values = $this->normalizer->child($row['values']);
            $family = $this->findFamily($index, $values);

            if ($family === null) {
                $errors[] = $this->rowError('الأولاد', $row['row'], 'لم يُعثر على رب الأسرة.');
                continue;
            }

            $child = $this->childAttributes($values);
            $validator = Validator::make($child, FamilyRules::child());

            if ($validator->fails()) {
                $errors[] = $this->rowError('الأولاد', $row['row'], $validator->errors()->first());
                continue;
            }

            $this->children->create($family, $validator->validated());
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function childAttributes(array $values): array
    {
        unset($values['head_of_family_name'], $values['family_national_id_number']);

        return $values;
    }

    /**
     * @param  array<string, Family>  $index
     */
    private function remember(array &$index, Family $family): void
    {
        $name = trim($family->head_of_family_name);
        $index[$name.'|'] = $family;
        $nationalId = trim((string) $family->national_id_number);
        $index[$name.'|'.$nationalId] = $family;
    }

    /**
     * @param  array<string, Family>  $index
     * @param  array<string, mixed>  $values
     */
    private function findFamily(array $index, array $values): ?Family
    {
        $name = trim((string) ($values['head_of_family_name'] ?? ''));
        $nationalId = trim((string) ($values['family_national_id_number'] ?? ''));

        return $index[$name.'|'.$nationalId] ?? $index[$name.'|'] ?? null;
    }

    private function rowError(string $sheet, int $row, string $message): string
    {
        return "{$sheet} صف {$row}: {$message}";
    }
}
