<?php

namespace App\Actions\Data;

use App\Services\Data\FamilyBackupService;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class RestoreFamilyBackupAction
{
    public function __construct(
        private FamilyBackupService $backup,
    ) {}

    public function execute(UploadedFile $file): int
    {
        $decoded = json_decode((string) $file->getContent(), true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'file' => 'ملف النسخة الاحتياطية غير صالح.',
            ]);
        }

        return $this->backup->restore($decoded);
    }
}
