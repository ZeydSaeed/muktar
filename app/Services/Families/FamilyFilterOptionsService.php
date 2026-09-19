<?php

namespace App\Services\Families;

use App\Models\Family;
use App\Support\HouseNumberGroup;
use Carbon\CarbonInterface;

class FamilyFilterOptionsService
{
    /**
     * @return array<string, array<int, array{value: string, label: string}>>
     */
    public function forTable(): array
    {
        return [
            'professions' => $this->strings('profession'),
            'house_groups' => $this->houseGroups(),
            'nearest_landmarks' => $this->strings('nearest_landmark'),
            'birth_years' => $this->years('birth_date'),
            'ration_card_numbers' => $this->integers('ration_card_number'),
            'housing_card_numbers' => $this->integers('housing_card_number'),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function strings(string $column): array
    {
        return Family::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn (mixed $value): array => $this->option((string) $value))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function integers(string $column): array
    {
        return Family::query()
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn (mixed $value): array => $this->option((string) $value))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function years(string $column): array
    {
        return Family::query()
            ->whereNotNull($column)
            ->get([$column])
            ->map(function (Family $family) use ($column): ?int {
                $date = $family->getAttribute($column);

                return $date instanceof CarbonInterface ? $date->year : null;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->map(fn (int $year): array => $this->option((string) $year))
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function houseGroups(): array
    {
        return Family::query()
            ->whereNotNull('house_number')
            ->pluck('house_number')
            ->map(fn (mixed $number): int => HouseNumberGroup::of((int) $number))
            ->unique()
            ->sort()
            ->values()
            ->map(fn (int $group): array => [
                'value' => (string) $group,
                'label' => HouseNumberGroup::label($group),
            ])
            ->all();
    }

    /**
     * @return array{value: string, label: string}
     */
    private function option(string $value): array
    {
        return ['value' => $value, 'label' => $value];
    }
}
