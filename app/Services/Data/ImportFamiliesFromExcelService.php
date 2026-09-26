<?php

namespace App\Services\Data;

use App\Data\ExcelImportResult;
use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use App\Support\ExcelFamilyKind;
use App\Support\FamilyExcelColumns;
use App\Support\FamilyRules;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportFamiliesFromExcelService
{
    public function __construct(
        private ExcelSheetReader $reader,
        private ExcelFamilyBlockParser $parser,
        private ExcelFamilyMemberMapper $mapper,
        private ExcelRowNormalizer $normalizer,
        private ExcelFamilyChildImporter $childImporter,
        private FamilyRepositoryInterface $families,
    ) {}

    public function import(UploadedFile $file): ExcelImportResult
    {
        $path = $file->getRealPath();

        if ($path === false) {
            return new ExcelImportResult(0, 0, ['تعذر قراءة الملف. تأكد من أن الملف سليم وغير تالف.']);
        }

        ini_set('memory_limit', '512M');
        set_time_limit(120);

        try {
            return DB::transaction(fn (): ExcelImportResult => $this->run($path));
        } catch (\Throwable $exception) {
            report($exception);

            return new ExcelImportResult(0, 0, [
                'تعذر استيراد الملف. الملف كبير أو تنسيقه ثقيل على إكسل. احفظه من إكسل كملف xlsx جديد ثم أعد المحاولة.',
            ]);
        }
    }

    private function run(string $path): ExcelImportResult
    {
        $rows = $this->reader->mappedRows(
            $path,
            (string) config('mukhtar.data.families_sheet'),
            FamilyExcelColumns::importHeaders(),
            true,
            true,
        );

        if ($rows === [] || ! $this->hasNameColumn($rows)) {
            return new ExcelImportResult(0, 0, [
                'تعذر قراءة الملف. أبقِ صف العناوين كما في القالب مع عمود «اسم رب الاسرة». كل عائلة تنتهي بصف فارغ: أب ثم زوجة ثم أبناء، أو أب وزوجة بلا أبناء، أو أرملة/زوجة متوفاة في عمود «اسم الزوجة او الزوج» ثم الأبناء.',
            ]);
        }

        return $this->importBlocks($this->parser->parse($rows));
    }

    /**
     * @param  list<array{kind: ExcelFamilyKind, startRow: int, head: array{row: int, values: array<string, mixed>}, wife: array{row: int, values: array<string, mixed>}|null, children: list<array{row: int, values: array<string, mixed>}>}>  $blocks
     */
    private function importBlocks(array $blocks): ExcelImportResult
    {
        $errors = [];
        $familyCount = 0;
        $childCount = 0;

        foreach ($blocks as $block) {
            $imported = $this->importBlock($block, $errors);
            $familyCount += $imported['families'];
            $childCount += $imported['children'];
        }

        if ($familyCount === 0 && $childCount === 0 && $errors === []) {
            $errors[] = 'لم يُعثر على أي عائلة. ضع كل عائلة في صفوف متتالية واترك صفاً فارغاً بينها.';
        }

        return new ExcelImportResult($familyCount, $childCount, $errors);
    }

    /**
     * @param  array{kind: ExcelFamilyKind, startRow: int, head: array{row: int, values: array<string, mixed>}, wife: array{row: int, values: array<string, mixed>}|null, children: list<array{row: int, values: array<string, mixed>}>}  $block
     * @param  list<string>  $errors
     * @return array{families: int, children: int}
     */
    private function importBlock(array $block, array &$errors): array
    {
        if ($block['kind'] === ExcelFamilyKind::Couple && $block['wife'] === null) {
            $errors[] = $this->missingWifeMessage($block['startRow']);

            return ['families' => 0, 'children' => 0];
        }

        $family = $this->storeFamily($block, $errors);

        if ($family === null) {
            return ['families' => 0, 'children' => 0];
        }

        return [
            'families' => 1,
            'children' => $this->childImporter->store($family, $block['children'], $errors),
        ];
    }

    /**
     * @param  array{kind: ExcelFamilyKind, startRow: int, head: array{row: int, values: array<string, mixed>}, wife: array{row: int, values: array<string, mixed>}|null, children: list<array{row: int, values: array<string, mixed>}>}  $block
     * @param  list<string>  $errors
     */
    private function storeFamily(array $block, array &$errors): ?Family
    {
        $head = Validator::make(
            $this->normalizer->family($this->mapper->father($block['head']['values'])),
            FamilyRules::family(),
        );

        if ($head->fails()) {
            $errors[] = $this->rowError($block['head']['row'], $this->headRole($block['kind']), $head->errors()->first());

            return null;
        }

        $payload = $head->validated();
        $wifeValues = $this->validatedWife($block, $errors);

        if ($block['wife'] !== null && $wifeValues === null) {
            return null;
        }

        return $this->families->create(array_merge($payload, $wifeValues ?? []));
    }

    /**
     * @param  array{kind: ExcelFamilyKind, startRow: int, head: array{row: int, values: array<string, mixed>}, wife: array{row: int, values: array<string, mixed>}|null, children: list<array{row: int, values: array<string, mixed>}>}  $block
     * @param  list<string>  $errors
     * @return array{spouse_name: mixed, spouse_profession: mixed}|null
     */
    private function validatedWife(array $block, array &$errors): ?array
    {
        if ($block['wife'] === null) {
            return null;
        }

        $wife = Validator::make($this->mapper->wife($block['wife']['values']), [
            'spouse_name' => ['required', 'string', 'max:255'],
            'spouse_profession' => FamilyRules::family()['spouse_profession'],
        ]);

        if ($wife->fails()) {
            $errors[] = $this->rowError($block['wife']['row'], 'الزوجة', $wife->errors()->first());

            return null;
        }

        return $wife->validated();
    }

    private function headRole(ExcelFamilyKind $kind): string
    {
        return $kind === ExcelFamilyKind::Widow ? 'الأرملة' : 'الأب';
    }

    private function missingWifeMessage(int $row): string
    {
        return "صف {$row}: بعد صف الأب يجب صف الزوجة ثم الأبناء أو صف فارغ. إن كانت أرملة فاكتب «ارملة» أو «ارمله» في عمود «اسم الزوجة او الزوج». إن كانت الزوجة متوفاة فاكتب «الزوجة متوفاة» أو «الزوجه متوفاه» أو «الزوجة متوفاه» في العمود نفسه، ثم ضع الأبناء مباشرة دون صف زوجة.";
    }

    /**
     * @param  list<array{row: int, values: array<string, mixed>}>  $rows
     */
    private function hasNameColumn(array $rows): bool
    {
        foreach ($rows as $row) {
            if (array_key_exists('head_of_family_name', $row['values'])) {
                return true;
            }
        }

        return false;
    }

    private function rowError(int $row, string $role, string $message): string
    {
        return "صف {$row} ({$role}): {$message}";
    }
}
