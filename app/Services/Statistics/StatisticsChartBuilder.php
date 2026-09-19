<?php

namespace App\Services\Statistics;

class StatisticsChartBuilder
{
    private const OTHER = 'أخرى';

    private const TOP_LIMIT = 8;

    public function __construct(
        private StatisticsSummaryBuilder $summary,
        private StatisticsBreakdownBuilder $breakdowns,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function report(): array
    {
        $summary = $this->summary->build();

        return [
            'title' => 'الإحصائية البيانية للاسكان العسكري',
            'generated_at' => now()->toDateString(),
            'kpis' => $this->kpis($summary),
            'compositions' => $this->compositions($summary),
            'bars' => $this->barCharts($summary),
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return list<array{label: string, value: int|float}>
     */
    private function kpis(array $summary): array
    {
        return [
            ['label' => 'العوائل', 'value' => $summary['families']],
            ['label' => 'الأولاد', 'value' => $summary['children']],
            ['label' => 'مجموع الأفراد', 'value' => $summary['people']],
            ['label' => 'معدل الأولاد', 'value' => $summary['average_children']],
            ['label' => 'عوائل لديها أولاد', 'value' => $summary['families_with_children']],
            ['label' => 'عوائل بلا أولاد', 'value' => $summary['families_without_children']],
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return list<array{title: string, rows: list<array{label: string, total: int, percent: float}>}>
     */
    private function compositions(array $summary): array
    {
        return [
            $this->pairChart('تركيب السكان', 'العوائل', (int) $summary['families'], 'الأولاد', (int) $summary['children']),
            $this->pairChart('الذكور والإناث', 'ذكور', (int) $summary['males'], 'إناث', (int) $summary['females']),
            $this->pairChart(
                'اكتمال بيانات الأولاد في العوائل',
                'لديها أولاد',
                (int) $summary['families_with_children'],
                'بلا أولاد',
                (int) $summary['families_without_children'],
            ),
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return list<array{title: string, rows: list<array{label: string, total: int, percent: float}>}>
     */
    private function barCharts(array $summary): array
    {
        return [
            ['title' => 'أبرز المهن لرب الأسرة', 'rows' => $this->top($this->breakdowns->familyColumn('profession'))],
            ['title' => 'مجموعات رقم الدار', 'rows' => $this->top($this->breakdowns->houseGroups())],
            ['title' => 'مواليد رب الأسرة', 'rows' => $this->top($this->breakdowns->familyYears('birth_date'))],
            ['title' => 'أبرز مهن الأولاد', 'rows' => $this->top($this->breakdowns->childColumn('profession'))],
            ['title' => 'النواقص في السجل', 'rows' => $this->missingBars($summary)],
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return list<array{label: string, total: int, percent: float}>
     */
    private function missingBars(array $summary): array
    {
        $families = max(1, (int) $summary['families']);

        return $this->withPercent([
            ['label' => 'بلا رقم موبايل', 'total' => (int) $summary['missing_mobile']],
            ['label' => 'بلا هوية وطنية', 'total' => (int) $summary['missing_national_id']],
            ['label' => 'بلا مهنة', 'total' => (int) $summary['missing_profession']],
            ['label' => 'بلا رقم دار', 'total' => (int) $summary['missing_house_number']],
            ['label' => 'بلا بطاقة تموينية', 'total' => (int) $summary['missing_ration_card']],
            ['label' => 'بلا بطاقة سكن', 'total' => (int) $summary['missing_housing_card']],
        ], $families);
    }

    /**
     * @return array{title: string, rows: list<array{label: string, total: int, percent: float}>}
     */
    private function pairChart(string $title, string $left, int $leftTotal, string $right, int $rightTotal): array
    {
        return [
            'title' => $title,
            'rows' => $this->withPercent([
                ['label' => $left, 'total' => $leftTotal],
                ['label' => $right, 'total' => $rightTotal],
            ], $leftTotal + $rightTotal),
        ];
    }

    /**
     * @param  list<array{label: string, total: int}>  $rows
     * @return list<array{label: string, total: int, percent: float}>
     */
    private function withPercent(array $rows, int $total): array
    {
        $base = max(1, $total);

        return array_map(fn (array $row): array => [
            'label' => $row['label'],
            'total' => $row['total'],
            'percent' => round(100 * $row['total'] / $base, 1),
        ], $rows);
    }

    /**
     * @param  list<array{label: string, total: int, percent: float}>  $rows
     * @return list<array{label: string, total: int, percent: float}>
     */
    private function top(array $rows): array
    {
        $known = array_values(array_filter($rows, fn (array $row): bool => $row['label'] !== 'غير محدد'));
        $head = array_slice($known, 0, self::TOP_LIMIT);
        $rest = array_slice($known, self::TOP_LIMIT);
        $other = array_sum(array_column($rest, 'total'));
        $total = max(1, array_sum(array_column($rows, 'total')));

        if ($other > 0) {
            $head[] = ['label' => self::OTHER, 'total' => $other, 'percent' => 0.0];
        }

        return $this->withPercent($head, $total);
    }
}
