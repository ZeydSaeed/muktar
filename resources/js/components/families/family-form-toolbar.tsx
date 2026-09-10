import { Link } from '@inertiajs/react';
import { Save, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    title: string;
    processing: boolean;
    closeHref?: string;
    saveLabel?: string;
};

export function FamilyFormToolbar({
    title,
    processing,
    closeHref = '/families',
    saveLabel = 'حفظ',
}: Props) {
    return (
        <div className="flex items-center justify-between gap-3">
            <h1 className="text-xl font-semibold">{title}</h1>
            <div className="flex items-center gap-2">
                <Button type="submit" disabled={processing}>
                    {processing ? <Spinner /> : <Save className="size-4" />}
                    {saveLabel}
                </Button>
                <Button variant="outline" asChild>
                    <Link href={closeHref}>
                        <X className="size-4" />
                        اغلاق
                    </Link>
                </Button>
            </div>
        </div>
    );
}
