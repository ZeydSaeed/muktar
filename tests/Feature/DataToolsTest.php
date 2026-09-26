<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Models\User;
use App\Services\Data\ExcelFamilySpreadsheetWriter;
use App\Services\Data\ExcelImportScenarioCatalog;
use App\Services\Data\ExcelTemplateService;
use App\Support\FamilyExcelColumns;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class DataToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_data_pages(): void
    {
        $this->get(route('data.excel-import'))->assertRedirect(route('home'));
        $this->get(route('data.backup'))->assertRedirect(route('home'));
        $this->get(route('data.restore'))->assertRedirect(route('home'));
    }

    public function test_authenticated_user_can_view_excel_import_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('data.excel-import'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('data/excel-import')
                ->where('guide.title', 'العوائل')
                ->has('guide.columns')
                ->has('guide.rows')
                ->where('guide.roles.0', 'الأب')
                ->where('guide.roles.1', 'الزوجة')
                ->where('guide.roles.4', 'صف فارغ')
                ->where('guide.rows.2.0', 'أحمد حسن علي'));
    }

    public function test_authenticated_user_can_download_excel_template(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('data.excel-import.template'));

        $response->assertOk();
        $disposition = $response->headers->get('content-disposition') ?? '';
        $this->assertStringContainsString('.xlsx', $disposition);
        $this->assertStringContainsString('attachment', $disposition);
    }

    public function test_excel_template_imports_every_reference_layout(): void
    {
        $path = sys_get_temp_dir().'/template-reference-'.uniqid().'.xlsx';
        file_put_contents($path, app(ExcelTemplateService::class)->contents());

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'قالب-استيراد-العوائل.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseCount('families', 9);
        $this->assertDatabaseCount('family_children', 12);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'حسن علي كاظم الجواد',
            'spouse_name' => 'فاطمة محمد علي',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'كاظم جواد محمد',
            'spouse_name' => 'زينب علي حسين',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'سعاد كاظم حسن',
            'spouse_name' => 'ارملة',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'حليمة جابر موسى',
            'spouse_name' => 'ارمله',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'ناهد ناصر كامل',
            'spouse_name' => 'ارملة',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'جواد حسن علي',
            'spouse_name' => 'الزوجة متوفاة',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'عباس جبار سلطان',
            'spouse_name' => 'الزوجه متوفاه',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'منصور شلج مرشد',
            'spouse_name' => 'الزوجة متوفاه',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'غالب حبيب جواد',
            'spouse_name' => 'الزوجة متوفاة',
        ]);
        $this->assertDatabaseHas('family_children', ['name' => 'أحمد حسن علي']);
        $this->assertDatabaseMissing('family_children', ['name' => 'سعاد كاظم حسن']);
    }

    public function test_authenticated_user_can_import_families_from_excel(): void
    {
        $path = $this->makeExcel([
            ['اسم رب الاسرة' => 'حسن علي', 'المهنة' => 'موظف'],
            ['اسم رب الاسرة' => 'فاطمة محمد', 'المهنة' => 'ربة بيت'],
            ['اسم رب الاسرة' => 'أحمد حسن', 'المهنة' => 'طالب'],
            [],
            ['اسم رب الاسرة' => 'كاظم جواد', 'المهنة' => 'معلم'],
            ['اسم رب الاسرة' => 'زينب علي', 'المهنة' => 'موظفة'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'حسن علي',
            'profession' => 'موظف',
            'spouse_name' => 'فاطمة محمد',
            'spouse_profession' => 'ربة بيت',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'كاظم جواد',
            'spouse_name' => 'زينب علي',
        ]);
        $this->assertDatabaseHas('family_children', [
            'name' => 'أحمد حسن',
            'profession' => 'طالب',
        ]);
        $this->assertDatabaseCount('families', 2);
        $this->assertDatabaseCount('family_children', 1);
    }

    public function test_excel_import_skips_family_when_wife_row_is_missing(): void
    {
        $path = $this->makeExcel([
            ['اسم رب الاسرة' => 'حسن علي', 'المهنة' => 'موظف'],
            [],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'error')
            ->assertSessionHas('inertia.flash_data.toast.message', function (string $message): bool {
                return str_contains($message, 'بعد صف الأب يجب صف الزوجة')
                    && str_contains($message, 'اسم الزوجة او الزوج')
                    && str_contains($message, 'ارملة')
                    && str_contains($message, 'الزوجة متوفاة');
            });

        $this->assertDatabaseCount('families', 0);
    }

    public function test_excel_import_treats_nameless_row_as_family_separator(): void
    {
        $path = $this->makeExcel([
            ['اسم رب الاسرة' => 'حسن علي'],
            ['اسم رب الاسرة' => 'فاطمة محمد'],
            ['المهنة' => 'طالب'],
            ['اسم رب الاسرة' => 'كاظم جواد'],
            ['اسم رب الاسرة' => 'زينب علي'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseHas('families', ['head_of_family_name' => 'حسن علي']);
        $this->assertDatabaseHas('families', ['head_of_family_name' => 'كاظم جواد']);
        $this->assertDatabaseCount('families', 2);
        $this->assertDatabaseCount('family_children', 0);
    }

    public function test_excel_import_rejects_file_without_family_headers(): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue([1, 1], 'عمود خاطئ');
        $sheet->setCellValue([1, 2], 'حسن علي');
        $path = sys_get_temp_dir().'/families-bad-headers-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'error')
            ->assertSessionHas('inertia.flash_data.toast.message', function (string $message): bool {
                return str_contains($message, 'صف العناوين');
            });

        $this->assertDatabaseCount('families', 0);
    }

    public function test_excel_import_accepts_register_header_aliases(): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('العوائل');
        $sheet->setCellValue([1, 1], 'اسم رب الاسرة الرباعي واللقب');
        $sheet->setCellValue([2, 1], 'اسم الزوجة');
        $sheet->setCellValue([3, 1], 'المهنة');
        $sheet->setCellValue([6, 1], 'رقم الدار');
        $sheet->setCellValue([7, 1], 'رقم البطاقة التموينية');
        $sheet->setCellValue([12, 1], 'المواليد');
        $sheet->setCellValue([35, 1], 'الملاحضات');
        $sheet->setCellValue([1, 2], 'عباس جبار');
        $sheet->setCellValue([2, 2], 'سعاد رزاق');
        $sheet->setCellValue([3, 2], 'كاسب');
        $sheet->setCellValue([6, 2], '76214');
        $sheet->setCellValue([7, 2], '0138597 كربلاء');
        $sheet->setCellValue([12, 2], 'غماس 1986');
        $sheet->setCellValue([1, 3], 'سعاد رزاق');
        $sheet->setCellValue([3, 3], 'ربة بيت');
        $sheet->setCellValue([1, 4], 'سجاد عباس');
        $sheet->setCellValue([3, 4], 'طالب');
        $sheet->setCellValue([12, 4], 'كربلاء 2009');
        $path = sys_get_temp_dir().'/families-aliases-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'عباس جبار',
            'spouse_name' => 'سعاد رزاق',
            'house_number' => 76214,
            'ration_card_number' => 138597,
            'birth_place' => 'غماس',
        ]);
        $this->assertDatabaseHas('family_children', ['name' => 'سجاد عباس']);
    }

    public function test_excel_import_accepts_widow_then_children(): void
    {
        $path = $this->makeExcel([
            [
                'اسم رب الاسرة' => 'سعاد كاظم',
                'اسم الزوجة او الزوج' => 'ارملة',
                'رقم الدار' => '33010',
            ],
            ['اسم رب الاسرة' => 'علي كاظم', 'المهنة' => 'طالب'],
            ['اسم رب الاسرة' => 'مريم كاظم', 'المهنة' => 'طالبة'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'سعاد كاظم',
            'spouse_name' => 'ارملة',
            'house_number' => 33010,
        ]);
        $family = Family::query()->where('head_of_family_name', 'سعاد كاظم')->firstOrFail();
        $this->assertSame('ارملة', $family->spouse_name);
        $this->assertDatabaseHas('family_children', ['family_id' => $family->id, 'name' => 'علي كاظم']);
        $this->assertDatabaseHas('family_children', ['family_id' => $family->id, 'name' => 'مريم كاظم']);
        $this->assertDatabaseCount('families', 1);
        $this->assertDatabaseCount('family_children', 2);
    }

    public function test_excel_import_accepts_widower_then_children(): void
    {
        $path = $this->makeExcel([
            [
                'اسم رب الاسرة' => 'جواد حسن',
                'المهنة' => 'متقاعد',
                'اسم الزوجة او الزوج' => 'الزوجه متوفاه',
                'رقم الدار' => '34020',
            ],
            ['اسم رب الاسرة' => 'حسين جواد', 'المهنة' => 'طالب'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $family = Family::query()->where('head_of_family_name', 'جواد حسن')->firstOrFail();
        $this->assertSame('الزوجه متوفاه', $family->spouse_name);
        $this->assertNotSame('حسين جواد', $family->spouse_name);
        $this->assertDatabaseHas('family_children', [
            'family_id' => $family->id,
            'name' => 'حسين جواد',
        ]);
        $this->assertDatabaseCount('families', 1);
        $this->assertDatabaseCount('family_children', 1);
    }

    public function test_excel_import_starts_widow_family_after_dot_separator(): void
    {
        $path = $this->makeExcel([
            [
                'اسم رب الاسرة' => 'حسن علي',
                'اسم الزوجة او الزوج' => 'فاطمة محمد',
            ],
            ['اسم رب الاسرة' => 'فاطمة محمد'],
            ['اسم رب الاسرة' => '.'],
            [
                'اسم رب الاسرة' => 'ضمياء شاكر',
                'اسم الزوجة او الزوج' => 'ارمله',
            ],
            ['اسم رب الاسرة' => 'عباس حسن'],
            ['الملاحظات' => 'ع'],
            [
                'اسم رب الاسرة' => 'جواد حسن',
                'اسم الزوجة او الزوج' => 'الزوجة متوفاة',
            ],
            ['اسم رب الاسرة' => 'حسين جواد'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $widow = Family::query()->where('head_of_family_name', 'ضمياء شاكر')->firstOrFail();
        $this->assertSame('ارمله', $widow->spouse_name);
        $this->assertDatabaseHas('family_children', [
            'family_id' => $widow->id,
            'name' => 'عباس حسن',
        ]);

        $widower = Family::query()->where('head_of_family_name', 'جواد حسن')->firstOrFail();
        $this->assertSame('الزوجة متوفاة', $widower->spouse_name);
        $this->assertNotSame('حسين جواد', $widower->spouse_name);
        $this->assertDatabaseHas('family_children', [
            'family_id' => $widower->id,
            'name' => 'حسين جواد',
        ]);

        $this->assertDatabaseCount('families', 3);
        $this->assertDatabaseMissing('family_children', ['name' => 'ضمياء شاكر']);
    }

    public function test_excel_import_loads_twenty_families_across_all_layouts(): void
    {
        $path = sys_get_temp_dir().'/families-20-'.uniqid().'.xlsx';
        app(ExcelFamilySpreadsheetWriter::class)->save(
            app(ExcelImportScenarioCatalog::class)->twentyFamilies(),
            $path,
        );

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'families-20.xlsx', null, null, true),
            ])
            ->assertRedirect()
            ->assertSessionHas('inertia.flash_data.toast.type', 'success');

        $this->assertDatabaseCount('families', 20);
        $this->assertDatabaseCount('family_children', 32);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'تجربة استيراد حسن علي كاظم-1',
            'spouse_name' => 'تجربة استيراد فاطمة محمد علي-1',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'تجربة استيراد كاظم جواد محمد-9',
            'spouse_name' => 'تجربة استيراد زينب علي حسين-9',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'تجربة استيراد سعاد كاظم حسن-13',
            'spouse_name' => 'ارملة',
        ]);
        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'تجربة استيراد جواد حسن علي-17',
            'spouse_name' => 'الزوجة متوفاة',
        ]);
        $widow = Family::query()->where('head_of_family_name', 'تجربة استيراد سعاد كاظم حسن-13')->firstOrFail();
        $this->assertSame('ارملة', $widow->spouse_name);
        $this->assertCount(2, $widow->children);
        $this->assertNotSame('تجربة استيراد علي كاظم حسن-13', $widow->spouse_name);
        $widower = Family::query()->where('head_of_family_name', 'تجربة استيراد جواد حسن علي-17')->firstOrFail();
        $this->assertCount(2, $widower->children);
        $this->assertSame('الزوجة متوفاة', $widower->spouse_name);
        $childless = Family::query()->where('head_of_family_name', 'تجربة استيراد كاظم جواد محمد-9')->firstOrFail();
        $this->assertCount(0, $childless->children);
    }

    public function test_authenticated_user_can_view_backup_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('data.backup'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('data/backup')
                ->has('filename'));
    }

    public function test_authenticated_user_can_save_backup_to_a_directory(): void
    {
        $user = User::factory()->create();
        Family::factory()->create(['head_of_family_name' => 'كاظم جواد']);
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'muktar-backup-'.uniqid();
        mkdir($directory);

        $this->actingAs($user)
            ->from(route('data.backup'))
            ->post(route('data.backup.store'), ['directory' => $directory])
            ->assertRedirect(route('data.backup'));

        $files = glob($directory.DIRECTORY_SEPARATOR.'*.json');
        $this->assertIsArray($files);
        $this->assertCount(1, $files);
        $payload = json_decode((string) file_get_contents($files[0]), true);
        $this->assertSame('muktar.families.v1', $payload['schema']);
    }

    public function test_backup_save_rejects_missing_directory(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('data.backup'))
            ->post(route('data.backup.store'), [
                'directory' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-'.uniqid(),
            ])
            ->assertRedirect(route('data.backup'))
            ->assertSessionHasErrors('directory');
    }

    public function test_authenticated_user_can_download_and_restore_backup(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create(['head_of_family_name' => 'كاظم جواد']);
        FamilyChild::factory()->create([
            'family_id' => $family->id,
            'name' => 'علي كاظم',
        ]);

        $download = $this->actingAs($user)->get(route('data.backup.download'));
        $download->assertOk();
        $json = $download->streamedContent();
        $payload = json_decode($json, true);
        $this->assertSame('muktar.families.v1', $payload['schema']);
        $this->assertCount(1, $payload['families']);

        $family->delete();
        $this->assertDatabaseCount('families', 0);

        $path = sys_get_temp_dir().'/backup-test.json';
        file_put_contents($path, $json);

        $this->actingAs($user)
            ->post(route('data.restore.store'), [
                'file' => new UploadedFile($path, 'backup.json', 'application/json', null, true),
                'confirm' => '1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('families', ['head_of_family_name' => 'كاظم جواد']);
        $this->assertDatabaseHas('family_children', ['name' => 'علي كاظم']);
    }

    /**
     * @param  list<array<string, string>>  $rows
     */
    private function makeExcel(array $rows): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('العوائل');
        $headers = array_keys(FamilyExcelColumns::familyHeaders());

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        foreach ($rows as $offset => $values) {
            $excelRow = $offset + 2;

            foreach ($values as $header => $value) {
                $this->setExcelValue($sheet, $headers, $header, $value, $excelRow);
            }
        }

        $path = sys_get_temp_dir().'/families-import-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }

    /**
     * @param  list<string>  $headers
     */
    private function setExcelValue(
        Worksheet $sheet,
        array $headers,
        string $header,
        string $value,
        int $row,
    ): void {
        $column = array_search($header, $headers, true);

        $this->assertNotFalse($column);
        $sheet->setCellValue([(int) $column + 1, $row], $value);
    }
}
