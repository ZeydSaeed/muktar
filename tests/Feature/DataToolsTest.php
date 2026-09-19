<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Models\User;
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
            ->assertInertia(fn (Assert $page) => $page->component('data/excel-import'));
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

    public function test_authenticated_user_can_import_families_from_excel(): void
    {
        $path = $this->makeExcel();

        $this->actingAs(User::factory()->create())
            ->post(route('data.excel-import.store'), [
                'file' => new UploadedFile($path, 'import.xlsx', null, null, true),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'حسن علي',
            'profession' => 'موظف',
        ]);
        $this->assertDatabaseHas('family_children', [
            'name' => 'أحمد حسن',
            'profession' => 'طالب',
        ]);
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

    private function makeExcel(): string
    {
        $spreadsheet = new Spreadsheet;
        $families = $spreadsheet->getActiveSheet();
        $families->setTitle('العوائل');
        $headers = array_keys(FamilyExcelColumns::familyHeaders());
        foreach ($headers as $index => $header) {
            $families->setCellValue([$index + 1, 1], $header);
        }
        $this->setExcelValue($families, $headers, 'اسم رب الاسرة', 'حسن علي');
        $this->setExcelValue($families, $headers, 'المهنة', 'موظف');

        $children = $spreadsheet->createSheet();
        $children->setTitle('الأولاد');
        $childHeaders = array_keys(FamilyExcelColumns::childHeaders());
        foreach ($childHeaders as $index => $header) {
            $children->setCellValue([$index + 1, 1], $header);
        }
        $children->setCellValue([1, 2], 'حسن علي');
        $children->setCellValue([3, 2], 'أحمد حسن');
        $children->setCellValue([8, 2], 'طالب');

        $path = sys_get_temp_dir().'/families-import.xlsx';
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
    ): void {
        $column = array_search($header, $headers, true);

        $this->assertNotFalse($column);
        $sheet->setCellValue([(int) $column + 1, 2], $value);
    }
}
