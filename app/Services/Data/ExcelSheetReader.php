<?php

namespace App\Services\Data;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelSheetReader
{
    /**
     * @param  array<string, string>  $headerMap
     * @return list<array{row: int, values: array<string, mixed>}>
     */
    public function mappedRows(
        string $path,
        string $sheetName,
        array $headerMap,
        bool $fallbackToFirst = false,
    ): array {
        $spreadsheet = IOFactory::load($path);
        $sheet = $this->sheetByName($spreadsheet, $sheetName);

        if ($sheet === null && $fallbackToFirst && $spreadsheet->getSheetCount() > 0) {
            $sheet = $spreadsheet->getSheet(0);
        }

        if ($sheet === null) {
            $spreadsheet->disconnectWorksheets();

            return [];
        }

        $rows = $this->rowsFromSheet($sheet, $headerMap);
        $spreadsheet->disconnectWorksheets();

        return $rows;
    }

    private function sheetByName(Spreadsheet $spreadsheet, string $sheetName): ?Worksheet
    {
        if ($spreadsheet->sheetNameExists($sheetName)) {
            return $spreadsheet->getSheetByName($sheetName);
        }

        return null;
    }

    /**
     * @param  array<string, string>  $headerMap
     * @return list<array{row: int, values: array<string, mixed>}>
     */
    private function rowsFromSheet(Worksheet $sheet, array $headerMap): array
    {
        $headers = $this->headerKeys($sheet, $headerMap);
        $rows = [];
        $highest = $sheet->getHighestDataRow();

        for ($row = 2; $row <= $highest; $row++) {
            $values = $this->rowValues($sheet, $row, $headers);

            if ($this->isEmpty($values)) {
                continue;
            }

            $rows[] = ['row' => $row, 'values' => $values];
        }

        return $rows;
    }

    /**
     * @param  array<string, string>  $headerMap
     * @return array<int, string>
     */
    private function headerKeys(Worksheet $sheet, array $headerMap): array
    {
        $keys = [];
        $highestColumn = $sheet->getHighestDataColumn();

        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator('A', $highestColumn) as $cell) {
                $label = trim((string) $cell->getValue());
                $column = $cell->getColumn();
                $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);

                if (isset($headerMap[$label])) {
                    $keys[$index] = $headerMap[$label];
                }
            }
        }

        return $keys;
    }

    /**
     * @param  array<int, string>  $headers
     * @return array<string, mixed>
     */
    private function rowValues(Worksheet $sheet, int $row, array $headers): array
    {
        $values = [];

        foreach ($headers as $columnIndex => $field) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $cell = $sheet->getCell($column.$row);
            $values[$field] = $this->cellValue($cell);
        }

        return $values;
    }

    private function cellValue(Cell $cell): mixed
    {
        $value = $cell->getValue();

        if ($value === null || $value === '') {
            return null;
        }

        if (ExcelDate::isDateTime($cell) && is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function isEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
