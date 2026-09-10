<?php

namespace App\Services\Families;

use App\Data\FamilyTableFilters;
use App\Models\Family;
use App\Support\FamilyFilterOperators;
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
        $this->birthYear($query, $filters->birthYear);
        $this->compareInt($query, 'males_count', $filters->malesOp, $filters->malesCount);
        $this->compareInt($query, 'females_count', $filters->femalesOp, $filters->femalesCount);
        $this->equalInt($query, 'ration_card_number', $filters->rationCardNumber);
        $this->equalInt($query, 'housing_card_number', $filters->housingCardNumber);
        $this->equalString($query, 'nearest_landmark', $filters->nearestLandmark);
        $this->dateRange(
            $query,
            'karbala_residence_date',
            $filters->karbalaOp,
            $filters->karbalaDate,
            $filters->karbalaDateTo,
        );
        $this->dateRange(
            $query,
            'current_residence_date',
            $filters->currentOp,
            $filters->currentDate,
            $filters->currentDateTo,
        );
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

        [$from, $to] = HouseNumberGroup::range($group);
        $query->whereBetween('house_number', [$from, $to]);
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

    /**
     * @param  Builder<Family>  $query
     */
    private function compareInt(Builder $query, string $column, ?string $op, ?int $value): void
    {
        if ($value === null || $op === null) {
            return;
        }

        if ($op === FamilyFilterOperators::GREATER_THAN) {
            $query->where($column, '>', $value);
        }

        if ($op === FamilyFilterOperators::LESS_THAN) {
            $query->where($column, '<', $value);
        }
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function dateRange(
        Builder $query,
        string $column,
        ?string $op,
        ?string $from,
        ?string $to,
    ): void {
        if ($op === FamilyFilterOperators::BEFORE && $from !== null) {
            $query->whereDate($column, '<', $from);
        }

        if ($op === FamilyFilterOperators::AFTER && $from !== null) {
            $query->whereDate($column, '>', $from);
        }

        if ($op === FamilyFilterOperators::BETWEEN && $from !== null && $to !== null) {
            $query->whereBetween($column, [$from, $to]);
        }
    }
}
