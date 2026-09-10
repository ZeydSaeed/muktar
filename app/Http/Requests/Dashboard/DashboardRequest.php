<?php

namespace App\Http\Requests\Dashboard;

use App\Data\FamilyTableFilters;
use App\Support\FamilyFilterOperators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'house_group' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'birth_year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:2100'],
            'males_op' => ['sometimes', 'nullable', Rule::in(FamilyFilterOperators::comparisons())],
            'males_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'females_op' => ['sometimes', 'nullable', Rule::in(FamilyFilterOperators::comparisons())],
            'females_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'ration_card_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'housing_card_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'nearest_landmark' => ['sometimes', 'nullable', 'string', 'max:255'],
            'karbala_op' => ['sometimes', 'nullable', Rule::in(FamilyFilterOperators::dates())],
            'karbala_date' => ['sometimes', 'nullable', 'date'],
            'karbala_date_to' => ['sometimes', 'nullable', 'date'],
            'current_op' => ['sometimes', 'nullable', Rule::in(FamilyFilterOperators::dates())],
            'current_date' => ['sometimes', 'nullable', 'date'],
            'current_date_to' => ['sometimes', 'nullable', 'date'],
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
            birthYear: $this->optionalInt('birth_year'),
            malesOp: $this->optionalString('males_op'),
            malesCount: $this->optionalInt('males_count'),
            femalesOp: $this->optionalString('females_op'),
            femalesCount: $this->optionalInt('females_count'),
            rationCardNumber: $this->optionalInt('ration_card_number'),
            housingCardNumber: $this->optionalInt('housing_card_number'),
            nearestLandmark: $this->optionalString('nearest_landmark'),
            karbalaOp: $this->optionalString('karbala_op'),
            karbalaDate: $this->optionalString('karbala_date'),
            karbalaDateTo: $this->optionalString('karbala_date_to'),
            currentOp: $this->optionalString('current_op'),
            currentDate: $this->optionalString('current_date'),
            currentDateTo: $this->optionalString('current_date_to'),
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
