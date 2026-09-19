export const BACKUP_DOWNLOAD_URL = '/data/backup/download';

export function isAbortError(error: unknown): boolean {
    return error instanceof DOMException && error.name === 'AbortError';
}

export function canPickSavePath(): boolean {
    return (
        typeof window !== 'undefined' &&
        typeof window.showSaveFilePicker === 'function'
    );
}

export async function fetchBackupBlob(url: string): Promise<Blob> {
    const response = await fetch(url, { credentials: 'same-origin' });

    if (!response.ok) {
        throw new Error('backup-download-failed');
    }

    return response.blob();
}

export async function writeBackupFile(
    handle: FileSystemFileHandle,
    blob: Blob,
): Promise<void> {
    const writable = await handle.createWritable();
    await writable.write(blob);
    await writable.close();
}

export async function pickBackupSavePath(
    suggestedName: string,
): Promise<FileSystemFileHandle> {
    if (window.showSaveFilePicker === undefined) {
        throw new Error('save-picker-unavailable');
    }

    return window.showSaveFilePicker({
        suggestedName,
        id: 'muktar-family-backup',
        startIn: 'desktop',
        types: [
            {
                description: 'JSON',
                accept: { 'application/json': ['.json'] },
            },
        ],
    });
}
