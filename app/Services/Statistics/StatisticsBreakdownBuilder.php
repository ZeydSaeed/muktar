<?php

namespace App\Services\Statistics;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Support\HouseNumberGroup;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class StatisticsBreakdownBuilder
{
    private const UNKNOWN = 'غير محدد';

    /**
     * @return list<array{label: string, total: int, percent: float}>
     */
    public function familyColumn(string $column): array
    {
        return $this->fromPairs(
            Family::query()->count(),
            Family::query()->pluck($column),
        );
    }

    /**
     * @return list<array{label: string, total: int, percent: float}>
     */
    public function childColumn(string $column): array
    {
        return $this->fromPairs(
            FamilyChild::query()->count(),
            FamilyChild::query()->pluck($column),
        );
    }

    /**
     * @return list<array{label: string, total: int, percent: float}>
     */
    public function houseGroups(): array
    {
        $numbers = Family::query()->pluck('house_number');

        $labels = $numbers->map(function (mixed $number): string {
            if ($number === null || $number === '') {
                return self::UNKNOWN;
            }

            return HouseNumberGroup::label(HouseNumberGroup::of((int) $number));
        });

        return $this->fromPairs($numbers->count(), $labels);
    }

    /**
     * @return list<array{label: string, total: int, percent: float}>
     */
    public function familyYears(string $column): array
    {
        return $this->fromPairs(
            Family::query()->count(),
            $this->years(Family::query()->get([$column]), $column),
        );
    }

    /**
     * @return list<array{label: string, total: int, percent: float}>
     */
    public function childYears(string $column): array
    {
        return $this->fromPairs(
            FamilyChild::query()->count(),
            $this->years(FamilyChild::query()->get([$column]), $column),
        );
    }

    /**
     * @param  Collection<int, mixed>  $models
     * @return Collection<int, string>
     */
    private function years(Collection $models, string $column): Collection
    {
        return $models->map(function (mixed $model) use ($column): string {
            $date = $model->getAttribute($column);

            return $date instanceof CarbonInterface
                ? (string) $date->year
                : self::UNKNOWN;
        });
    }

    /**
     * @param  Collection<int, mixed>  $values
     * @return list<array{label: string, total: int, percent: float}>
     */
    private function fromPairs(int $total, Collection $values): array
    {
        $counts = $values
            ->map(fn (mixed $value): string => $this->label($value))
            ->countBy()
            ->sortDesc();

        return $counts
            ->map(fn (int $count, string $label): array => [
                'label' => $label,
                'total' => $count,
                'percent' => $total === 0 ? 0.0 : round(100 * $count / $total, 1),
            ])
            ->values()
            ->all();
    }

    private function label(mixed $value): string
    {
        if ($value === null || $value === '') {
            return self::UNKNOWN;
        }

        return trim((string) $value);
    }
}
