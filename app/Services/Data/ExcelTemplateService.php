<?php

namespace App\Services\Data;

class ExcelTemplateService
{
    public function __construct(
        private ExcelFamilySpreadsheetWriter $writer,
        private ExcelTemplateReferenceCatalog $catalog,
    ) {}

    public function filename(): string
    {
        return 'قالب-استيراد-العوائل.xlsx';
    }

    public function contents(): string
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('excel-template-', true).'.xlsx';
        $this->writer->save($this->catalog->families(), $path);
        $contents = (string) file_get_contents($path);
        unlink($path);

        return $contents;
    }
}
