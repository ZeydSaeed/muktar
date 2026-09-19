import { useState } from 'react';

export function useSelectedIds(): {
    ids: number[];
    toggle: (id: number) => void;
    togglePage: (pageIds: number[]) => void;
    clear: () => void;
    isSelected: (id: number) => boolean;
} {
    const [ids, setIds] = useState<number[]>([]);

    return {
        ids,
        toggle: (id) => setIds((current) => toggleId(current, id)),
        togglePage: (pageIds) => setIds((current) => nextPageSelection(current, pageIds)),
        clear: () => setIds([]),
        isSelected: (id) => ids.includes(id),
    };
}

export function pageCheckboxState(
    selectedIds: number[],
    pageIds: number[],
): boolean | 'indeterminate' {
    if (pageIds.length === 0) {
        return false;
    }

    const selectedCount = pageIds.filter((id) => selectedIds.includes(id)).length;

    if (selectedCount === 0) {
        return false;
    }

    return selectedCount === pageIds.length ? true : 'indeterminate';
}

function toggleId(current: number[], id: number): number[] {
    return current.includes(id)
        ? current.filter((item) => item !== id)
        : [...current, id];
}

function nextPageSelection(current: number[], pageIds: number[]): number[] {
    const allSelected = pageIds.every((id) => current.includes(id));

    if (allSelected) {
        return current.filter((id) => !pageIds.includes(id));
    }

    return [...new Set([...current, ...pageIds])];
}
