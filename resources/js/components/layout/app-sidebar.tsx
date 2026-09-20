import { Link } from '@inertiajs/react';
import AppLogoIcon from '@/components/layout/app-logo-icon';
import { NavMain } from '@/components/layout/nav-main';
import { NavUser } from '@/components/layout/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { mainNavItems } from '@/lib/main-nav';
import { dashboard } from '@/routes';

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset" side="right" className="print:hidden">
            <SidebarHeader className="items-center justify-center py-4">
                <Link
                    href={dashboard()}
                    prefetch
                    aria-label="الصفحة الرئيسية"
                    className="flex items-center justify-center"
                >
                    <span className="bg-sidebar-primary text-sidebar-primary-foreground flex size-10 items-center justify-center rounded-lg group-data-[collapsible=icon]:size-8">
                        <AppLogoIcon className="text-brand-navy size-6 group-data-[collapsible=icon]:size-4" />
                    </span>
                </Link>
            </SidebarHeader>

            <SidebarContent className="py-4">
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter className="pt-4">
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
