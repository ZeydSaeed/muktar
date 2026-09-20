<?php

namespace App\Http\Requests\Auth;

use App\Support\MukhtarPin;
use Illuminate\Foundation\Http\FormRequest;

class EnterMukhtarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'pin' => MukhtarPin::rules(),
        ];
    }

    public function pin(): string
    {
        return (string) $this->validated('pin');
    }
}
