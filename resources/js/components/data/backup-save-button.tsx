import { Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useBackupSave } from '@/hooks/use-backup-save';

type Props = {
    filename: string;
};

export function BackupSaveButton({ filename }: Props) {
    const { saving, downloadUrl, onSaveClick } = useBackupSave(filename);

    return (
        <Button asChild>
            <a
                href={downloadUrl}
                download={filename}
                aria-disabled={saving}
                onClick={(event) => void onSaveClick(event)}
            >
                {saving ? <Spinner /> : <Save className="size-4" />}
                حفظ النسخة الاحتياطية
            </a>
        </Button>
    );
}
