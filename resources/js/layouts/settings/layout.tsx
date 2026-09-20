import type { PropsWithChildren } from 'react';
import { SettingsCloseButton } from '@/components/settings/settings-close-button';
import { SettingsLogoutButton } from '@/components/settings/settings-logout-button';

export default function SettingsLayout({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-0 flex-1 flex-col overflow-hidden px-4 py-2">
            <div className="flex shrink-0 items-center justify-between gap-3">
                <h1 className="text-xl font-semibold tracking-tight">
                    الإعدادات
                </h1>
                <div className="flex items-center gap-2">
                    <SettingsCloseButton />
                    <SettingsLogoutButton />
                </div>
            </div>

            <div className="mt-2 min-h-0 flex-1 overflow-hidden">{children}</div>
        </div>
    );
}
