<?php

namespace App\Http\Requests\Dashboard;

use App\Data\FamilyTableFilters;
use App\Support\HouseNumberGroup;
use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'family_children' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:255'],
            'house_group' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:'.(HouseNumberGroup::TWO_DIGIT_MODULUS - 1),
            ],
            'nearest_landmark' => ['sometimes', 'nullable', 'string', 'max:255'],
            'birth_year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:2100'],
            'ration_card_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'housing_card_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->exists('search')) {
            return;
        }

        $normalized = preg_replace('/\s+/u', ' ', (string) $this->input('search'));
        $search = trim((string) $normalized);

        $this->merge([
            'search' => $search === '' ? null : $search,
        ]);
    }

    public function searchTerm(): ?string
    {
        $search = $this->validated('search', null);

        return is_string($search) && $search !== '' ? $search : null;
    }

    public function filters(): FamilyTableFilters
    {
        return new FamilyTableFilters(
            profession: $this->optionalString('profession'),
            houseGroup: $this->optionalInt('house_group'),
            nearestLandmark: $this->optionalString('nearest_landmark'),
            birthYear: $this->optionalInt('birth_year'),
            rationCardNumber: $this->optionalInt('ration_card_number'),
            housingCardNumber: $this->optionalInt('housing_card_number'),
        );
    }

    private function optionalString(string $key): ?string
    {
        $value = $this->validated($key, null);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function optionalInt(string $key): ?int
    {
        if (! $this->has($key) || $this->input($key) === '' || $this->input($key) === null) {
            return null;
        }

        return $this->integer($key);
    }
}
