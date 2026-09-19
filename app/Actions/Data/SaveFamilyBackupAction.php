<?php

namespace App\Actions\Data;

use App\Support\BackupStoragePath;
use Illuminate\Support\Facades\File;

class SaveFamilyBackupAction
{
    public function __construct(
        private ExportFamilyBackupAction $export,
    ) {}

    public function execute(string $directory): string
    {
        $file = $this->export->execute();
        $folder = BackupStoragePath::resolveWritable($directory);
        $path = $folder.DIRECTORY_SEPARATOR.$file['filename'];

        File::put($path, $file['contents']);

        return $path;
    }

    public function executeToPath(string $filePath): string
    {
        $file = $this->export->execute();
        $folder = BackupStoragePath::resolveWritable(dirname($filePath));
        $name = $this->jsonFileName(basename($filePath), $file['filename']);
        $path = $folder.DIRECTORY_SEPARATOR.$name;

        File::put($path, $file['contents']);

        return $path;
    }

    private function jsonFileName(string $name, string $fallback): string
    {
        if ($name === '' || $name === '.' || $name === '..') {
            return $fallback;
        }

        if (! str_ends_with(strtolower($name), '.json')) {
            return $name.'.json';
        }

        return $name;
    }
}
