import { usePage } from '@inertiajs/react';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs: _breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    const { neighborhood } = usePage().props;

    return (
        <header className="border-sidebar-border/50 relative flex h-12 shrink-0 items-center border-b px-4">
            <SidebarTrigger className="relative z-10 -ms-1" />
            <h1 className="pointer-events-none absolute inset-x-0 text-center text-sm font-semibold">
                {neighborhood}
            </h1>
        </header>
    );
}
