<?php

namespace App\Actions\Data;

use App\Data\ExcelImportResult;
use App\Services\Data\ImportFamiliesFromExcelService;
use Illuminate\Http\UploadedFile;

class ImportFamiliesFromExcelAction
{
    public function __construct(
        private ImportFamiliesFromExcelService $import,
    ) {}

    public function execute(UploadedFile $file): ExcelImportResult
    {
        return $this->import->import($file);
    }
}
