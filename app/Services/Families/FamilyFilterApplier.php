<?php

namespace App\Services\Families;

use App\Data\FamilyTableFilters;
use App\Models\Family;
use App\Support\HouseNumberGroup;
use Illuminate\Database\Eloquent\Builder;

class FamilyFilterApplier
{
    /**
     * @param  Builder<Family>  $query
     */
    public function apply(Builder $query, FamilyTableFilters $filters): void
    {
        $this->equalString($query, 'profession', $filters->profession);
        $this->houseGroup($query, $filters->houseGroup);
        $this->equalString($query, 'nearest_landmark', $filters->nearestLandmark);
        $this->birthYear($query, $filters->birthYear);
        $this->equalInt($query, 'ration_card_number', $filters->rationCardNumber);
        $this->equalInt($query, 'housing_card_number', $filters->housingCardNumber);
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function equalString(Builder $query, string $column, ?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where($column, $value);
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function equalInt(Builder $query, string $column, ?int $value): void
    {
        if ($value === null) {
            return;
        }

        $query->where($column, $value);
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function houseGroup(Builder $query, ?int $group): void
    {
        if ($group === null) {
            return;
        }

        $query->whereRaw(HouseNumberGroup::sqlExpression().' = ?', [$group]);
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function birthYear(Builder $query, ?int $year): void
    {
        if ($year === null) {
            return;
        }

        $query->whereYear('birth_date', $year);
    }
}
