import { Link } from '@inertiajs/react';
import AppLogo from '@/components/layout/app-logo';
import { NavMain } from '@/components/layout/nav-main';
import { NavUser } from '@/components/layout/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { mainNavItems } from '@/lib/main-nav';
import { dashboard } from '@/routes';

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset" side="right" className="print:hidden">
            <SidebarHeader className="pb-6">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="sm" asChild>
                            <Link
                                href={dashboard()}
                                prefetch
                                className="flex w-full items-center"
                            >
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
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
