import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Props = {
    selectedIds: number[];
    onDeleted: () => void;
};

export function FamilyDeletionToolbar({ selectedIds, onDeleted }: Props) {
    const count = selectedIds.length;

    return (
        <div className="flex shrink-0 items-center justify-end gap-3">
            {count > 0 ? (
                <p className="text-muted-foreground text-[13px]">
                    تم تحديد {count}
                </p>
            ) : null}
            <Button
                type="button"
                variant="destructive"
                disabled={count === 0}
                onClick={() => deleteSelected(selectedIds, onDeleted)}
            >
                <Trash2 className="size-4" />
                حذف
            </Button>
        </div>
    );
}

function deleteSelected(ids: number[], onDeleted: () => void): void {
    if (ids.length === 0 || !window.confirm(confirmMessage(ids.length))) {
        return;
    }

    router.delete('/families', {
        data: { ids },
        preserveScroll: true,
        onSuccess: onDeleted,
    });
}

function confirmMessage(count: number): string {
    return count === 1
        ? 'هل تريد حذف العائلة المحددة؟'
        : `هل تريد حذف ${count} عوائل؟`;
}
