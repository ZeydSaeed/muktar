import { Head } from '@inertiajs/react';
import { FamiliesTable } from '@/components/families/families-table';
import type {
    ChildrenModal,
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
    childrenModal?: ChildrenModal | null;
};

export default function Dashboard({
    families,
    search = '',
    filters,
    filters_active,
    filterOptions,
    childrenModal = null,
}: Props) {
    return (
        <>
            <Head title="اللوحة الرئيسية" />
            <div className="flex h-full min-h-0 flex-1 flex-col overflow-hidden p-4">
                <FamiliesTable
                    families={families}
                    search={search}
                    filters={filters}
                    filters_active={filters_active}
                    filterOptions={filterOptions}
                    childrenModal={childrenModal}
                />
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [],
};
