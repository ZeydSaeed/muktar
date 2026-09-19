import { Head } from '@inertiajs/react';
import { BackupSaveButton } from '@/components/data/backup-save-button';
import { DataToolPanel } from '@/components/data/data-tool-panel';

type Props = {
    families_count: number;
    children_count: number;
    filename: string;
};

export default function BackupPage({
    families_count,
    children_count,
    filename,
}: Props) {
    return (
        <>
            <Head title="نسخ احتياطي" />
            <DataToolPanel title="نسخ احتياطي">
                <p className="text-sm">
                    المسجّل حالياً: {families_count} عائلة و{children_count} ابناً.
                </p>
                <BackupSaveButton filename={filename} />
            </DataToolPanel>
        </>
    );
}

BackupPage.layout = {
    breadcrumbs: [],
};
