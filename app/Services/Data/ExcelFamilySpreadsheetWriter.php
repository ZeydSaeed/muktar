<?php

namespace App\Services\Data;

use App\Support\FamilyExcelColumns;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelFamilySpreadsheetWriter
{
    /**
     * @param  list<list<array<string, string>>>  $blocks
     */
    public function save(array $blocks, string $path): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle((string) config('mukhtar.data.families_sheet'));
        $headers = array_keys(FamilyExcelColumns::familyHeaders());
        $sheet->setRightToLeft(true);
        $sheet->freezePane('A2');
        $this->writeHeaders($sheet, $headers);
        $this->writeBlocks($sheet, $headers, $blocks);
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @param  list<string>  $headers
     */
    private function writeHeaders(Worksheet $sheet, array $headers): void
    {
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<array<string, string>>>  $blocks
     */
    private function writeBlocks(Worksheet $sheet, array $headers, array $blocks): void
    {
        $row = 2;

        foreach ($blocks as $members) {
            foreach ($members as $values) {
                $this->writeRow($sheet, $headers, $row, $values);
                $row++;
            }

            $row++;
        }
    }

    /**
     * @param  list<string>  $headers
     * @param  array<string, string>  $values
     */
    private function writeRow(Worksheet $sheet, array $headers, int $row, array $values): void
    {
        foreach ($values as $header => $value) {
            $column = array_search($header, $headers, true);

            if ($column === false) {
                continue;
            }

            $sheet->getCell([(int) $column + 1, $row])->setValueExplicit(
                $value,
                DataType::TYPE_STRING,
            );
        }
    }
}
