<?php

namespace App\Data;

readonly class FamilyTableFilters
{
    public function __construct(
        public ?string $profession = null,
        public ?int $houseGroup = null,
        public ?string $nearestLandmark = null,
        public ?int $birthYear = null,
        public ?int $rationCardNumber = null,
        public ?int $housingCardNumber = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toFrontend(): array
    {
        return [
            'profession' => $this->profession ?? '',
            'house_group' => $this->stringify($this->houseGroup),
            'nearest_landmark' => $this->nearestLandmark ?? '',
            'birth_year' => $this->stringify($this->birthYear),
            'ration_card_number' => $this->stringify($this->rationCardNumber),
            'housing_card_number' => $this->stringify($this->housingCardNumber),
        ];
    }

    public function isActive(): bool
    {
        foreach ($this->toFrontend() as $value) {
            if ($value !== '') {
                return true;
            }
        }

        return false;
    }

    private function stringify(?int $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
