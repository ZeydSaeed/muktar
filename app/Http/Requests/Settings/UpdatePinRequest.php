<?php

namespace App\Http\Requests\Settings;

use App\Support\MukhtarPin;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePinRequest extends FormRequest
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
            'current_pin' => MukhtarPin::rules(),
            'pin' => array_merge(MukhtarPin::rules(confirmed: true), ['different:current_pin']),
        ];
    }

    public function currentPin(): string
    {
        return (string) $this->validated('current_pin');
    }

    public function pin(): string
    {
        return (string) $this->validated('pin');
    }
}
