import { Head } from '@inertiajs/react';
import { FamilyDeletionTable } from '@/components/families/family-deletion-table';
import { FamilyDeletionToolbar } from '@/components/families/family-deletion-toolbar';
import { useSelectedIds } from '@/hooks/use-selected-ids';
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
};

export default function FamiliesDeletion({
    families,
    search = '',
    filters,
    filters_active,
    filterOptions,
}: Props) {
    const selection = useSelectedIds();

    return (
        <>
            <Head title="حذف عائلة" />
            <div className="flex h-full min-h-0 flex-1 flex-col gap-2 overflow-hidden p-4">
                <FamilyDeletionToolbar
                    selectedIds={selection.ids}
                    onDeleted={selection.clear}
                />
                <FamilyDeletionTable
                    families={families}
                    search={search}
                    filters={filters}
                    filters_active={filters_active}
                    filterOptions={filterOptions}
                    selectedIds={selection.ids}
                    onToggle={selection.toggle}
                    onTogglePage={selection.togglePage}
                />
            </div>
        </>
    );
}

FamiliesDeletion.layout = {
    breadcrumbs: [],
};
