<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class BackupStoragePath
{
    public static function resolveWritable(string $directory): string
    {
        $normalized = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $directory), DIRECTORY_SEPARATOR);
        $real = realpath($normalized);

        if ($real === false || ! is_dir($real)) {
            throw ValidationException::withMessages([
                'directory' => 'المجلد غير موجود.',
            ]);
        }

        if (! is_writable($real)) {
            throw ValidationException::withMessages([
                'directory' => 'لا يمكن الكتابة في هذا المجلد.',
            ]);
        }

        return $real;
    }
}
