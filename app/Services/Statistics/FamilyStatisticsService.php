<?php

namespace App\Services\Statistics;

class FamilyStatisticsService
{
    public function __construct(
        private StatisticsSummaryBuilder $summary,
        private StatisticsBreakdownBuilder $breakdowns,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function report(): array
    {
        return [
            'title' => 'إحصائية الاسكان العسكري',
            'generated_at' => now()->toDateString(),
            'summary' => $this->summary->build(),
            'sections' => $this->sections(),
        ];
    }

    /**
     * @return list<array{title: string, rows: list<array{label: string, total: int, percent: float}>}>
     */
    private function sections(): array
    {
        return [
            $this->section('توزيع المهن لرب الأسرة', $this->breakdowns->familyColumn('profession')),
            $this->section('مجموعات رقم الدار', $this->breakdowns->houseGroups()),
            $this->section('نقاط دالة', $this->breakdowns->familyColumn('nearest_landmark')),
            $this->section('محافظة السكن الحالي', $this->breakdowns->familyColumn('current_governorate')),
            $this->section('محل ولادة رب الأسرة', $this->breakdowns->familyColumn('birth_place')),
            $this->section('مواليد رب الأسرة', $this->breakdowns->familyYears('birth_date')),
            $this->section('مهن الأولاد', $this->breakdowns->childColumn('profession')),
            $this->section('مواليد الأولاد', $this->breakdowns->childYears('birth_date')),
        ];
    }

    /**
     * @param  list<array{label: string, total: int, percent: float}>  $rows
     * @return array{title: string, rows: list<array{label: string, total: int, percent: float}>}
     */
    private function section(string $title, array $rows): array
    {
        return [
            'title' => $title,
            'rows' => $rows,
        ];
    }
}
