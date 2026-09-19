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

function setFilter(key: FamilyFilterKey, value: string): void {
    visitFamilyTable({ [key]: value });
}

export function FamiliesFilterPanel({ filters, options, active }: Props) {
    return (
        <aside className="border-primary bg-filter-panel text-primary-foreground flex h-full min-h-0 w-56 shrink-0 flex-col self-stretch overflow-hidden rounded-lg border">
            <div className="bg-primary text-primary-foreground flex items-center justify-between gap-1 px-2 py-2">
                <h2 className="text-[13px] font-semibold">الفلاتر</h2>
                <Button
                    type="button"
                    size="sm"
                    variant="secondary"
                    disabled={!active}
                    className="h-7 px-2 text-[13px]"
                    onClick={clearFamilyFilters}
                >
                    مسح
                </Button>
            </div>
            <div className="flex min-h-0 flex-1 flex-col gap-5 overflow-hidden p-3">
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
                    id="nearest_landmark"
                    label="نقطة دالة"
                    value={filters.nearest_landmark}
                    options={options.nearest_landmarks}
                    onChange={(value) => setFilter('nearest_landmark', value)}
                />
                <FilterSelect
                    id="birth_year"
                    label="المواليد"
                    value={filters.birth_year}
                    options={options.birth_years}
                    onChange={(value) => setFilter('birth_year', value)}
                />
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
            </div>
        </aside>
    );
}
