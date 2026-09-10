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
    birth_year: '',
    males_op: '',
    males_count: '',
    females_op: '',
    females_count: '',
    ration_card_number: '',
    housing_card_number: '',
    nearest_landmark: '',
    karbala_op: '',
    karbala_date: '',
    karbala_date_to: '',
    current_op: '',
    current_date: '',
    current_date_to: '',
});

const emptyOptions = (): FamilyFilterOptions => ({
    professions: [],
    house_groups: [],
    birth_years: [],
    males_counts: [],
    females_counts: [],
    ration_card_numbers: [],
    housing_card_numbers: [],
    nearest_landmarks: [],
    karbala_dates: [],
    current_dates: [],
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
        <div className="flex min-h-0 flex-1 flex-col gap-4">
            <div className="flex min-h-0 flex-1 gap-2">
                <div className="border-primary bg-card flex min-w-0 flex-1 flex-col overflow-hidden rounded-xl border-2">
                    <FamiliesSearchBar search={search} />
                <div className="overflow-x-auto">
                    <table className="w-full min-w-[800px] border-collapse text-right text-sm">
                        <thead className="bg-primary text-primary-foreground">
                            <tr>
                                <th className="border-primary border px-3 py-3 font-semibold">
                                    ت
                                </th>
                                <th className="border-primary border px-3 py-3 font-semibold">
                                    اسم رب الاسرة
                                </th>
                                <th className="border-primary border px-3 py-3 font-semibold">
                                    رقم الدار
                                </th>
                                <th className="border-primary border px-3 py-3 font-semibold">
                                    رقم الموبايل
                                </th>
                                <th className="border-primary border px-3 py-3 font-semibold">
                                    رقم البطاقة الوطنية
                                </th>
                                <th className="border-primary border px-3 py-3 text-center font-semibold">
                                    الأولاد
                                </th>
                                <th className="border-primary border px-3 py-3 text-center font-semibold">
                                    سجل العائلة
                                </th>
                                <th className="border-primary border px-3 py-3 text-center font-semibold">
                                    إجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {families.data.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className="border-primary text-muted-foreground border px-3 py-10 text-center"
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
                                        <td className="border-primary border px-3 py-3">
                                            {startingNumber + index}
                                        </td>
                                        <td className="border-primary border px-3 py-3 font-medium">
                                            <HighlightedName
                                                name={family.head_of_family_name}
                                                query={search}
                                            />
                                        </td>
                                        <td className="border-primary border px-3 py-3">
                                            {display(family.house_number)}
                                        </td>
                                        <td
                                            className="border-primary border px-3 py-3"
                                            dir="ltr"
                                        >
                                            {display(family.mobile_number)}
                                        </td>
                                        <td
                                            className="border-primary border px-3 py-3"
                                            dir="ltr"
                                        >
                                            {display(family.national_id_number)}
                                        </td>
                                        <td className="border-primary border px-3 py-2 text-center">
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="secondary"
                                                onClick={() =>
                                                    openChildrenModal(family.id)
                                                }
                                            >
                                                <Users className="size-4" />
                                                الأولاد
                                                {family.children_count > 0
                                                    ? ` (${family.children_count})`
                                                    : ''}
                                            </Button>
                                        </td>
                                        <td className="border-primary border px-3 py-2 text-center">
                                            <Button
                                                type="button"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={`/families/${family.id}/record`}
                                                >
                                                    <BookOpen className="size-4" />
                                                    سجل العائلة
                                                </Link>
                                            </Button>
                                        </td>
                                        <td className="border-primary border px-3 py-2">
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
                <div className="flex flex-col items-center gap-2 border-t-2 border-primary px-3 py-3 sm:flex-row sm:justify-between">
                    <p className="text-muted-foreground text-xs">
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
