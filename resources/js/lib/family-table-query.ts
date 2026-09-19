import { router } from '@inertiajs/react';

export const FAMILY_TABLE_PARTIALS = [
    'families',
    'search',
    'filters',
    'filters_active',
    'filterOptions',
    'childrenModal',
] as const;

export const FAMILY_FILTER_KEYS = [
    'profession',
    'house_group',
    'nearest_landmark',
    'birth_year',
    'ration_card_number',
    'housing_card_number',
] as const;

export type FamilyFilterKey = (typeof FAMILY_FILTER_KEYS)[number];

export function visitFamilyTable(
    updates: Partial<Record<FamilyFilterKey | 'search', string | null>>,
): void {
    const url = new URL(window.location.href);

    Object.entries(updates).forEach(([key, value]) => {
        if (value === null || value === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
    });

    url.searchParams.delete('page');

    router.get(`${url.pathname}${url.search}`, {}, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: [...FAMILY_TABLE_PARTIALS],
    });
}

export function clearFamilyFilters(): void {
    const cleared = Object.fromEntries(
        FAMILY_FILTER_KEYS.map((key) => [key, '']),
    ) as Record<FamilyFilterKey, string>;

    visitFamilyTable(cleared);
}
