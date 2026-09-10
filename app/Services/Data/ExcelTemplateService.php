<?php

namespace App\Services\Data;

use App\Support\FamilyExcelColumns;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelTemplateService
{
    public function filename(): string
    {
        return 'قالب-استيراد-العوائل.xlsx';
    }

    public function contents(): string
    {
        $spreadsheet = $this->makeSpreadsheet();
        $writer = new Xlsx($spreadsheet);
        $stream = fopen('php://temp', 'r+');

        if ($stream === false) {
            throw new \RuntimeException('تعذر إنشاء ملف القالب.');
        }

        $writer->save($stream);
        rewind($stream);
        $contents = stream_get_contents($stream) ?: '';
        fclose($stream);
        $spreadsheet->disconnectWorksheets();

        return $contents;
    }

    private function makeSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $families = $spreadsheet->getActiveSheet();
        $families->setTitle((string) config('mukhtar.data.families_sheet'));
        $this->writeHeaders($families, array_keys(FamilyExcelColumns::familyHeaders()));
        $families->setCellValue('A2', 'مثال: حسن علي');

        $children = $spreadsheet->createSheet();
        $children->setTitle((string) config('mukhtar.data.children_sheet'));
        $this->writeHeaders($children, array_keys(FamilyExcelColumns::childHeaders()));
        $children->setCellValue('A2', 'حسن علي');
        $children->setCellValue('C2', 'أحمد حسن');

        return $spreadsheet;
    }

    /**
     * @param  list<string>  $headers
     */
    private function writeHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $headers): void
    {
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }
    }
}
