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
        <div className="bg-card border-b px-4 py-3">
            <div className="mx-auto flex w-full max-w-6xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div className="space-y-0.5">
                    <h1 className="font-semibold">{title}</h1>
                    <p className="text-muted-foreground hidden sm:block">
                        استمارة بيانات العائلة حسب أقسام السجل
                    </p>
                </div>
                <div className="flex shrink-0 items-center gap-2">
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
        </div>
    );
}
