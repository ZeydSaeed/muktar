<?php

namespace App\Services\Data;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelSpreadsheetLoader
{
    public function load(string $path, string $sheetName): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($path);
        $this->configure($reader, $sheetName);

        try {
            return $reader->load($path);
        } catch (\Throwable) {
            $reader = IOFactory::createReaderForFile($path);
            $this->configure($reader, null);

            return $reader->load($path);
        }
    }

    private function configure(IReader $reader, ?string $sheetName): void
    {
        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(true);
        }

        if (method_exists($reader, 'setReadEmptyCells')) {
            $reader->setReadEmptyCells(false);
        }

        if ($sheetName !== null && $sheetName !== '' && method_exists($reader, 'setLoadSheetsOnly')) {
            $reader->setLoadSheetsOnly([$sheetName]);
        }
    }
}
