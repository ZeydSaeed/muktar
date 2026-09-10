<?php

namespace App\Http\Requests\Families;

use App\Support\FamilyRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyRequest extends FormRequest
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
        return FamilyRules::family();
    }
}
