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

export default function FamiliesDeletion({
    families,
    search = '',
    filters,
    filters_active,
    filterOptions,
    childrenModal = null,
}: Props) {
    return (
        <>
            <Head title="حذف عائلة" />
            <div className="flex h-full flex-1 flex-col p-4">
                <h1 className="mb-4 text-xl font-semibold">حذف عائلة</h1>
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

FamiliesDeletion.layout = {
    breadcrumbs: [],
};
