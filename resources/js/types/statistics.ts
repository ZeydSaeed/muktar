export type StatisticsCountRow = {
    label: string;
    total: number;
    percent: number;
};

export type StatisticsSection = {
    title: string;
    rows: StatisticsCountRow[];
};

export type StatisticsSummary = {
    families: number;
    children: number;
    people: number;
    families_with_children: number;
    families_without_children: number;
    average_children: number;
    males: number;
    females: number;
    recorded_members: number;
    missing_mobile: number;
    missing_national_id: number;
    missing_profession: number;
    missing_house_number: number;
    missing_ration_card: number;
    missing_housing_card: number;
};

export type StatisticsReport = {
    title: string;
    generated_at: string;
    summary: StatisticsSummary;
    sections: StatisticsSection[];
};

export type StatisticsKpi = {
    label: string;
    value: number;
};

export type StatisticsChartReport = {
    title: string;
    generated_at: string;
    kpis: StatisticsKpi[];
    compositions: StatisticsSection[];
    bars: StatisticsSection[];
};
