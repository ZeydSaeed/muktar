import { type MouseEvent, useState } from 'react';
import { toast } from 'sonner';
import {
    BACKUP_DOWNLOAD_URL,
    canPickSavePath,
    fetchBackupBlob,
    isAbortError,
    pickBackupSavePath,
    writeBackupFile,
} from '@/lib/save-backup-file';

export function useBackupSave(filename: string) {
    const [saving, setSaving] = useState(false);

    async function onSaveClick(
        event: MouseEvent<HTMLAnchorElement>,
    ): Promise<void> {
        if (saving) {
            event.preventDefault();

            return;
        }

        if (!canPickSavePath()) {
            return;
        }

        event.preventDefault();
        await savePickedFile(filename, setSaving);
    }

    return { saving, downloadUrl: BACKUP_DOWNLOAD_URL, onSaveClick };
}

async function savePickedFile(
    filename: string,
    setSaving: (value: boolean) => void,
): Promise<void> {
    try {
        const handle = await pickBackupSavePath(filename);
        setSaving(true);
        await writeBackupFile(handle, await fetchBackupBlob(BACKUP_DOWNLOAD_URL));
        toast.success('تم حفظ النسخة الاحتياطية');
    } catch (error) {
        if (isAbortError(error)) {
            return;
        }

        window.location.assign(BACKUP_DOWNLOAD_URL);
    } finally {
        setSaving(false);
    }
}
