<?php

namespace App\Services\Statistics;

use App\Models\Family;
use App\Models\FamilyChild;
use Illuminate\Database\Eloquent\Builder;

class StatisticsSummaryBuilder
{
    /**
     * @return array<string, int|float>
     */
    public function build(): array
    {
        $families = Family::query()->count();
        $children = FamilyChild::query()->count();
        $withChildren = Family::query()->has('children')->count();

        return [
            'families' => $families,
            'children' => $children,
            'people' => $families + $children,
            'families_with_children' => $withChildren,
            'families_without_children' => $families - $withChildren,
            'average_children' => $families === 0 ? 0.0 : round($children / $families, 2),
            'males' => (int) Family::query()->sum('males_count'),
            'females' => (int) Family::query()->sum('females_count'),
            'recorded_members' => (int) Family::query()->sum('family_members_count'),
            'missing_mobile' => $this->missingText('mobile_number'),
            'missing_national_id' => $this->missingText('national_id_number'),
            'missing_profession' => $this->missingText('profession'),
            'missing_house_number' => Family::query()->whereNull('house_number')->count(),
            'missing_ration_card' => Family::query()->whereNull('ration_card_number')->count(),
            'missing_housing_card' => Family::query()->whereNull('housing_card_number')->count(),
        ];
    }

    private function missingText(string $column): int
    {
        return Family::query()
            ->where(function (Builder $query) use ($column): void {
                $query->whereNull($column)->orWhere($column, '');
            })
            ->count();
    }
}
