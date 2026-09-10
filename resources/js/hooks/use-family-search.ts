import { visitFamilyTable } from '@/lib/family-table-query';
import { FAMILY_SEARCH_DEBOUNCE_MS } from '@/lib/family-search';
import { useEffect, useState } from 'react';

export function useFamilySearch(activeSearch: string): {
    value: string;
    setValue: (value: string) => void;
    clearSearch: () => void;
} {
    const [value, setValue] = useState(activeSearch);

    useEffect(() => {
        setValue(activeSearch);
    }, [activeSearch]);

    useEffect(() => {
        const next = value.trim();
        const current = activeSearch.trim();

        if (next === current) {
            return;
        }

        const timer = window.setTimeout(() => {
            visitFamilyTable({ search: next });
        }, FAMILY_SEARCH_DEBOUNCE_MS);

        return () => window.clearTimeout(timer);
    }, [value, activeSearch]);

    return {
        value,
        setValue,
        clearSearch: () => {
            setValue('');
            visitFamilyTable({ search: '' });
        },
    };
}
