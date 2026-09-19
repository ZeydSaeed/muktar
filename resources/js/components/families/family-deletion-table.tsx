import { FamiliesFilterPanel } from '@/components/families/families-filter-panel';
import { FamiliesSearchBar } from '@/components/families/families-search-bar';
import { FamilyDeletionRow } from '@/components/families/family-deletion-row';
import { TablePagination } from '@/components/families/table-pagination';
import { Checkbox } from '@/components/ui/checkbox';
import { pageCheckboxState } from '@/hooks/use-selected-ids';
import type {
    FamilyFilterOptions,
    FamilyTableFilters,
    PaginatedFamilies,
} from '@/types';

type Props = {
    families: PaginatedFamilies;
    search?: string;
    filters?: FamilyTableFilters;
    filters_active?: boolean;
    filterOptions?: FamilyFilterOptions;
    selectedIds: number[];
    onToggle: (id: number) => void;
    onTogglePage: (ids: number[]) => void;
};

const emptyFilters = (): FamilyTableFilters => ({
    profession: '',
    house_group: '',
    nearest_landmark: '',
    birth_year: '',
    ration_card_number: '',
    housing_card_number: '',
});

const emptyOptions = (): FamilyFilterOptions => ({
    professions: [],
    house_groups: [],
    nearest_landmarks: [],
    birth_years: [],
    ration_card_numbers: [],
    housing_card_numbers: [],
});

const headerClass =
    'border-primary sticky top-0 border px-2 py-1.5 text-center font-semibold';
const cellClass = 'border-primary border px-2 py-1';
const headers = [
    'ت',
    'اسم رب الاسرة',
    'رقم الدار',
    'رقم الموبايل',
    'البطاقة التموينية',
] as const;

export function FamilyDeletionTable({
    families,
    search = '',
    filters,
    filters_active = false,
    filterOptions,
    selectedIds,
    onToggle,
    onTogglePage,
}: Props) {
    const startingNumber = families.from ?? 1;
    const pageIds = families.data.map((family) => family.id);
    const selectAllState = pageCheckboxState(selectedIds, pageIds);

    return (
        <div className="flex min-h-0 flex-1 gap-2 overflow-hidden">
            <div className="border-primary bg-card flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden rounded-xl border-2">
                <FamiliesSearchBar search={search} />
                <div className="min-h-0 flex-1 overflow-auto">
                    <table className="w-full min-w-200 border-collapse text-right text-[13px]">
                        <thead className="bg-primary text-primary-foreground">
                            <tr>
                                <th className={`${headerClass} w-10`}>
                                    <Checkbox
                                        checked={selectAllState}
                                        onCheckedChange={() => onTogglePage(pageIds)}
                                        aria-label="تحديد كل العوائل في الصفحة"
                                        className="border-primary-foreground data-[state=checked]:bg-primary-foreground data-[state=checked]:text-primary"
                                    />
                                </th>
                                {headers.map((title) => (
                                    <th key={title} className={headerClass}>
                                        {title}
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {families.data.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className={`${cellClass} text-muted-foreground py-6 text-center`}
                                    >
                                        {search.trim() === '' && !filters_active
                                            ? 'لا توجد عائلات مسجّلة بعد'
                                            : 'لا توجد نتائج للبحث'}
                                    </td>
                                </tr>
                            ) : (
                                families.data.map((family, index) => (
                                    <FamilyDeletionRow
                                        key={family.id}
                                        family={family}
                                        sequence={startingNumber + index}
                                        search={search}
                                        checked={selectedIds.includes(family.id)}
                                        onToggle={onToggle}
                                    />
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
                <div className="flex shrink-0 flex-col items-center gap-1 border-t-2 border-primary px-2 py-1 sm:flex-row sm:justify-between">
                    <p className="text-muted-foreground text-[13px]">
                        {families.total > 0
                            ? `عرض ${families.from} إلى ${families.to} من أصل ${families.total}`
                            : 'لا توجد نتائج'}
                    </p>
                    <TablePagination links={families.links} />
                </div>
            </div>
            <FamiliesFilterPanel
                filters={filters ?? emptyFilters()}
                options={filterOptions ?? emptyOptions()}
                active={filters_active}
            />
        </div>
    );
}
