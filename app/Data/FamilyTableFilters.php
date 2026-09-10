<?php

namespace App\Data;

readonly class FamilyTableFilters
{
    public function __construct(
        public ?string $profession = null,
        public ?int $houseGroup = null,
        public ?int $birthYear = null,
        public ?string $malesOp = null,
        public ?int $malesCount = null,
        public ?string $femalesOp = null,
        public ?int $femalesCount = null,
        public ?int $rationCardNumber = null,
        public ?int $housingCardNumber = null,
        public ?string $nearestLandmark = null,
        public ?string $karbalaOp = null,
        public ?string $karbalaDate = null,
        public ?string $karbalaDateTo = null,
        public ?string $currentOp = null,
        public ?string $currentDate = null,
        public ?string $currentDateTo = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toFrontend(): array
    {
        return [
            'profession' => $this->profession ?? '',
            'house_group' => $this->stringify($this->houseGroup),
            'birth_year' => $this->stringify($this->birthYear),
            'males_op' => $this->malesOp ?? '',
            'males_count' => $this->stringify($this->malesCount),
            'females_op' => $this->femalesOp ?? '',
            'females_count' => $this->stringify($this->femalesCount),
            'ration_card_number' => $this->stringify($this->rationCardNumber),
            'housing_card_number' => $this->stringify($this->housingCardNumber),
            'nearest_landmark' => $this->nearestLandmark ?? '',
            'karbala_op' => $this->karbalaOp ?? '',
            'karbala_date' => $this->karbalaDate ?? '',
            'karbala_date_to' => $this->karbalaDateTo ?? '',
            'current_op' => $this->currentOp ?? '',
            'current_date' => $this->currentDate ?? '',
            'current_date_to' => $this->currentDateTo ?? '',
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
