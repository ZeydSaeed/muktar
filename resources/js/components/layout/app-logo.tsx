import { usePage } from '@inertiajs/react';

import AppLogoIcon from '@/components/layout/app-logo-icon';

export default function AppLogo() {
    const { name } = usePage().props;

    return (
        <div className="flex min-w-0 items-center gap-1.5">
            <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-7 shrink-0 items-center justify-center rounded-md">
                <AppLogoIcon className="text-brand-navy size-4 shrink-0" />
            </div>
            <div className="grid min-w-0 flex-1 text-right text-[13px] group-data-[collapsible=icon]:hidden">
                <span className="truncate leading-tight font-semibold">
                    {name}
                </span>
            </div>
        </div>
    );
}
