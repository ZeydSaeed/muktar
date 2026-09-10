import { DateFilterGroup } from '@/components/families/date-filter-group';
import { FilterSelect } from '@/components/families/filter-select';
import { Button } from '@/components/ui/button';
import type { FamilyFilterKey } from '@/lib/family-table-query';
import { clearFamilyFilters, visitFamilyTable } from '@/lib/family-table-query';
import type { FamilyFilterOptions, FamilyTableFilters } from '@/types';

type Props = {
    filters: FamilyTableFilters;
    options: FamilyFilterOptions;
    active: boolean;
};

const comparisonOptions = [
    { value: 'gt', label: '>' },
    { value: 'lt', label: '<' },
];

function setFilter(key: FamilyFilterKey, value: string): void {
    visitFamilyTable({ [key]: value });
}

export function FamiliesFilterPanel({ filters, options, active }: Props) {
    return (
        <aside className="border-primary bg-card flex w-44 shrink-0 flex-col overflow-hidden rounded-lg border">
            <div className="bg-primary text-primary-foreground flex items-center justify-between gap-1 px-2 py-1">
                <h2 className="text-[11px] font-semibold">الفلاتر</h2>
                <Button
                    type="button"
                    size="sm"
                    variant="secondary"
                    disabled={!active}
                    className="h-6 px-2 text-[10px]"
                    onClick={clearFamilyFilters}
                >
                    مسح
                </Button>
            </div>
            <div className="flex min-h-0 flex-1 flex-col gap-1.5 overflow-y-auto p-2">
                <FilterSelect
                    id="profession"
                    label="المهنة"
                    value={filters.profession}
                    options={options.professions}
                    onChange={(value) => setFilter('profession', value)}
                />
                <FilterSelect
                    id="house_group"
                    label="رقم الدار"
                    value={filters.house_group}
                    options={options.house_groups}
                    onChange={(value) => setFilter('house_group', value)}
                />
                <FilterSelect
                    id="birth_year"
                    label="المواليد"
                    value={filters.birth_year}
                    options={options.birth_years}
                    onChange={(value) => setFilter('birth_year', value)}
                />
                <div className="grid grid-cols-2 gap-1">
                    <FilterSelect
                        id="males_op"
                        label="الذكور"
                        value={filters.males_op}
                        options={comparisonOptions}
                        allLabel="—"
                        onChange={(value) => setFilter('males_op', value)}
                    />
                    <FilterSelect
                        id="males_count"
                        label="العدد"
                        value={filters.males_count}
                        options={options.males_counts}
                        onChange={(value) => setFilter('males_count', value)}
                    />
                </div>
                <div className="grid grid-cols-2 gap-1">
                    <FilterSelect
                        id="females_op"
                        label="الإناث"
                        value={filters.females_op}
                        options={comparisonOptions}
                        allLabel="—"
                        onChange={(value) => setFilter('females_op', value)}
                    />
                    <FilterSelect
                        id="females_count"
                        label="العدد"
                        value={filters.females_count}
                        options={options.females_counts}
                        onChange={(value) => setFilter('females_count', value)}
                    />
                </div>
                <FilterSelect
                    id="ration_card_number"
                    label="البطاقة التموينية"
                    value={filters.ration_card_number}
                    options={options.ration_card_numbers}
                    onChange={(value) => setFilter('ration_card_number', value)}
                />
                <FilterSelect
                    id="housing_card_number"
                    label="بطاقة السكن"
                    value={filters.housing_card_number}
                    options={options.housing_card_numbers}
                    onChange={(value) => setFilter('housing_card_number', value)}
                />
                <FilterSelect
                    id="nearest_landmark"
                    label="نقطة دالة"
                    value={filters.nearest_landmark}
                    options={options.nearest_landmarks}
                    onChange={(value) => setFilter('nearest_landmark', value)}
                />
                <DateFilterGroup
                    opId="karbala_op"
                    opLabel="سكن كربلاء"
                    opValue={filters.karbala_op}
                    dateValue={filters.karbala_date}
                    dateToValue={filters.karbala_date_to}
                    dates={options.karbala_dates}
                    dateKey="karbala_date"
                    dateToKey="karbala_date_to"
                    opKey="karbala_op"
                />
                <DateFilterGroup
                    opId="current_op"
                    opLabel="السكن الحالي"
                    opValue={filters.current_op}
                    dateValue={filters.current_date}
                    dateToValue={filters.current_date_to}
                    dates={options.current_dates}
                    dateKey="current_date"
                    dateToKey="current_date_to"
                    opKey="current_op"
                />
            </div>
        </aside>
    );
}
