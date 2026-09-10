<?php

namespace App\Http\Requests\Families;

use App\Support\FamilyRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'birth_date' => $this->blankToNull('birth_date'),
            'national_id_number' => $this->blankToNull('national_id_number'),
            'registry_number' => $this->blankToNull('registry_number'),
            'page_number' => $this->blankToNull('page_number'),
            'profession' => $this->blankToNull('profession'),
        ]);
    }

    private function blankToNull(string $key): mixed
    {
        $value = $this->input($key);

        return $value === '' || $value === null ? null : $value;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return FamilyRules::child();
    }
}
