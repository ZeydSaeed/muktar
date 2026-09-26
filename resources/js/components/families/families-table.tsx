import { FamiliesFilterPanel } from '@/components/families/families-filter-panel';
import { FamiliesSearchBar } from '@/components/families/families-search-bar';
import { FamilyChildrenDialog } from '@/components/families/family-children-dialog';
import { FamilyRowActions } from '@/components/families/family-row-actions';
import { HighlightedName } from '@/components/families/highlighted-name';
import { TablePagination } from '@/components/families/table-pagination';
import { Button } from '@/components/ui/button';
import type {
    ChildrenModal,
    FamilyFilterOptions,
    FamilyTableFilters,
    PaginatedFamilies,
} from '@/types';
import { Link, router } from '@inertiajs/react';
import { BookOpen, Users } from 'lucide-react';

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

type Props = {
    families: PaginatedFamilies;
    search?: string;
    filters?: FamilyTableFilters;
    filters_active?: boolean;
    filterOptions?: FamilyFilterOptions;
    childrenModal?: ChildrenModal | null;
};

const emptyValue = '—';
const headerClass =
    'border-primary sticky top-0 border px-2 py-1.5 text-center align-middle font-semibold';
const cellClass = 'border-primary border px-2 py-1 text-center align-middle';

function display(value: string | number | null): string {
    if (value === null || value === '') {
        return emptyValue;
    }

    return String(value);
}

function openChildrenModal(familyId: number): void {
    const url = new URL(window.location.href);
    url.searchParams.set('family_children', String(familyId));
    router.get(
        `${url.pathname}${url.search}`,
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['childrenModal', 'families'],
        },
    );
}

export function FamiliesTable({
    families,
    search = '',
    filters,
    filters_active = false,
    filterOptions,
    childrenModal = null,
}: Props) {
    const startingNumber = families.from ?? 1;
    const activeFilters = filters ?? emptyFilters();
    const options = filterOptions ?? emptyOptions();

    return (
        <div className="flex min-h-0 flex-1 flex-col overflow-hidden">
            <div className="flex min-h-0 flex-1 gap-2 overflow-hidden">
                <div className="border-primary bg-card flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden rounded-xl border-2">
                    <FamiliesSearchBar search={search} />
                <div className="min-h-0 flex-1 overflow-auto">
                    <table className="w-full min-w-200 border-collapse text-center text-[13px]">
                        <thead className="bg-primary text-primary-foreground">
                            <tr>
                                <th className={headerClass}>ت</th>
                                <th className={headerClass}>اسم رب الاسرة</th>
                                <th className={headerClass}>رقم الدار</th>
                                <th className={headerClass}>رقم الموبايل</th>
                                <th className={headerClass}>البطاقة التموينية</th>
                                <th className={headerClass}>الأولاد</th>
                                <th className={headerClass}>سجل العائلة</th>
                                <th className={headerClass}>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {families.data.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className={`${cellClass} text-muted-foreground py-6 text-center`}
                                    >
                                            {search.trim() === '' && !filters_active
                                                ? 'لا توجد عائلات مسجّلة بعد'
                                                : 'لا توجد نتائج للبحث'}
                                    </td>
                                </tr>
                            ) : (
                                families.data.map((family, index) => (
                                    <tr
                                        key={family.id}
                                        className="even:bg-muted/40"
                                    >
                                        <td className={`${cellClass} text-center`}>
                                            {startingNumber + index}
                                        </td>
                                        <td className={`${cellClass} font-medium`}>
                                            <HighlightedName
                                                name={family.head_of_family_name}
                                                query={search}
                                            />
                                        </td>
                                        <td className={`${cellClass} text-center`}>
                                            {display(family.house_number)}
                                        </td>
                                        <td
                                            className={`${cellClass} text-center`}
                                            dir="ltr"
                                        >
                                            {display(family.mobile_number)}
                                        </td>
                                        <td className={`${cellClass} text-center`}>
                                            {display(family.ration_card_number)}
                                        </td>
                                        <td className={`${cellClass} text-center`}>
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="secondary"
                                                className="h-7 px-2 text-[13px]"
                                                onClick={() =>
                                                    openChildrenModal(family.id)
                                                }
                                            >
                                                <Users className="size-4" />
                                                الأولاد ({family.children_count})
                                            </Button>
                                        </td>
                                        <td className={`${cellClass} text-center`}>
                                            <Button
                                                type="button"
                                                size="sm"
                                                asChild
                                                className="h-7 px-2 text-[13px]"
                                            >
                                                <Link
                                                    href={`/families/${family.id}/record`}
                                                >
                                                    <BookOpen className="size-4" />
                                                    سجل العائلة
                                                </Link>
                                            </Button>
                                        </td>
                                        <td className={`${cellClass} text-center`}>
                                            <FamilyRowActions
                                                familyId={family.id}
                                                familyName={
                                                    family.head_of_family_name
                                                }
                                            />
                                        </td>
                                    </tr>
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
                    filters={activeFilters}
                    options={options}
                    active={filters_active}
                />
            </div>

            <FamilyChildrenDialog childrenModal={childrenModal} />
        </div>
    );
}
