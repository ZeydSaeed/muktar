<?php

namespace App\Http\Requests\Data;

use Illuminate\Foundation\Http\FormRequest;

class SaveBackupRequest extends FormRequest
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
        return [
            'directory' => ['required', 'string', 'max:1024'],
        ];
    }

    public function directory(): string
    {
        return (string) $this->validated('directory');
    }
}
