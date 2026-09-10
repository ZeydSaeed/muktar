import { Head } from '@inertiajs/react';
import { Download } from 'lucide-react';
import { DataToolPanel } from '@/components/data/data-tool-panel';
import { Button } from '@/components/ui/button';

type Props = {
    families_count: number;
    children_count: number;
};

export default function BackupPage({
    families_count,
    children_count,
}: Props) {
    return (
        <>
            <Head title="نسخ احتياطي" />
            <DataToolPanel
                title="نسخ احتياطي"
                description="يحفظ ملف JSON يحتوي على جميع العوائل والأولاد لاسترداده لاحقاً."
            >
                <p className="text-sm">
                    المسجّل حالياً: {families_count} عائلة و{children_count} ابناً.
                </p>
                <Button asChild>
                    <a href="/data/backup/download">
                        <Download className="size-4" />
                        تنزيل النسخة الاحتياطية
                    </a>
                </Button>
            </DataToolPanel>
        </>
    );
}

BackupPage.layout = {
    breadcrumbs: [],
};
