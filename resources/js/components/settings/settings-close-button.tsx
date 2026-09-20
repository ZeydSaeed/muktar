import { Link } from '@inertiajs/react';
import { X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

export function SettingsCloseButton() {
    return (
        <Button variant="outline" asChild>
            <Link href={dashboard()}>
                <X className="size-4" />
                اغلاق
            </Link>
        </Button>
    );
}
