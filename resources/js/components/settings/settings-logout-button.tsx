import { router } from '@inertiajs/react';
import { LogOut } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { logout } from '@/routes';

export function SettingsLogoutButton() {
    return (
        <Button
            type="button"
            variant="outline"
            onClick={() => {
                router.post(logout.url(), {}, { onFinish: () => router.flushAll() });
            }}
        >
            <LogOut className="size-4" />
            خروج
        </Button>
    );
}
