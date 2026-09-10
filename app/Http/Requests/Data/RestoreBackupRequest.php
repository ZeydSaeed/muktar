<?php

namespace App\Http\Requests\Data;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class RestoreBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        $max = (int) config('mukhtar.data.max_upload_kilobytes');

        return [
            'file' => ['required', 'file', "max:{$max}", 'extensions:json'],
            'confirm' => ['accepted'],
        ];
    }

    public function uploadedFile(): UploadedFile
    {
        $file = $this->file('file');
        assert($file instanceof UploadedFile);

        return $file;
    }
}
