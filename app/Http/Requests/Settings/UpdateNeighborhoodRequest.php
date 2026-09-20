<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNeighborhoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'neighborhood' => trim((string) $this->input('neighborhood')),
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'neighborhood' => ['required', 'string', 'max:255'],
        ];
    }

    public function neighborhood(): string
    {
        return trim((string) $this->validated('neighborhood'));
    }
}
