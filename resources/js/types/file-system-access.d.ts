interface SaveFilePickerType {
    description?: string;
    accept: Record<string, string[]>;
}

interface SaveFilePickerOptions {
    suggestedName?: string;
    id?: string;
    startIn?: 'desktop' | 'documents' | 'downloads';
    excludeAcceptAllOption?: boolean;
    types?: SaveFilePickerType[];
}

interface Window {
    showSaveFilePicker?: (
        options?: SaveFilePickerOptions,
    ) => Promise<FileSystemFileHandle>;
}
