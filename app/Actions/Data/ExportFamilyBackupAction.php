<?php

namespace App\Actions\Data;

use App\Services\Data\FamilyBackupService;

class ExportFamilyBackupAction
{
    public function __construct(
        private FamilyBackupService $backup,
    ) {}

    /**
     * @return array{filename: string, contents: string}
     */
    public function execute(): array
    {
        return [
            'filename' => $this->backup->filename(),
            'contents' => json_encode(
                $this->backup->payload(),
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
            ) ?: '{}',
        ];
    }
}
