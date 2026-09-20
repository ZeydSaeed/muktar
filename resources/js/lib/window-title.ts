const FALLBACK_APP_NAME = 'الاسكان العسكري';

export function windowTitle(pageTitle: string, neighborhood: string): string {
    const brand = neighborhood.trim() || FALLBACK_APP_NAME;

    return pageTitle.trim() ? `${pageTitle} - ${brand}` : brand;
}
