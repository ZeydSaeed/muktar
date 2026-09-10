export const FAMILY_SEARCH_DEBOUNCE_MS = 350;

export function escapeRegExp(value: string): string {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

export function searchTokens(query: string): string[] {
    return query.trim().split(/\s+/).filter(Boolean);
}
