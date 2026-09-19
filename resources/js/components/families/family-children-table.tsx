import { router } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { FamilyChildRow } from '@/types';

type Props = {
    familyId: number;
    items: FamilyChildRow[];
    onEdit: (child: FamilyChildRow) => void;
    onDeleted: (childId: number) => void;
};

const emptyValue = '—';
const cellClass = 'border-primary border px-2 py-2';
const birthCellClass =
    'border-primary border min-w-[11rem] px-3 py-2 text-center text-[15px] whitespace-nowrap';

function deleteChild(
    familyId: number,
    child: FamilyChildRow,
    onDeleted: (childId: number) => void,
): void {
    if (!window.confirm(`هل تريد حذف ${child.name}؟`)) {
        return;
    }

    router.delete(`/families/${familyId}/children/${child.id}`, {
        preserveScroll: true,
        preserveState: true,
        only: ['childrenModal', 'families'],
        onSuccess: () => onDeleted(child.id),
    });
}

export function FamilyChildrenTable({
    familyId,
    items,
    onEdit,
    onDeleted,
}: Props) {
    return (
        <div className="border-primary overflow-hidden rounded-lg border-2">
            <table className="w-full border-collapse text-sm">
                <thead className="bg-primary text-primary-foreground">
                    <tr>
                        <th className={`${cellClass} text-center font-semibold`}>
                            ت
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            اسم الابن
                        </th>
                        <th className={`${birthCellClass} font-semibold`}>
                            التولد
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            رقم هوية البطاقة الوطنية
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            السجل
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            الصحيفة
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            المهنة
                        </th>
                        <th className={`${cellClass} text-center font-semibold`}>
                            إجراءات
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {items.length === 0 ? (
                        <tr>
                            <td
                                colSpan={8}
                                className="border-primary border px-3 py-6 text-center"
                            >
                                لا يوجد أولاد مسجّلون بعد
                            </td>
                        </tr>
                    ) : (
                        items.map((child, index) => (
                            <tr key={child.id}>
                                <td className={cellClass}>{index + 1}</td>
                                <td className={cellClass}>{child.name}</td>
                                <td className={birthCellClass} dir="ltr">
                                    {child.birth_date ?? emptyValue}
                                </td>
                                <td className={cellClass}>
                                    {child.national_id_number ?? emptyValue}
                                </td>
                                <td className={cellClass}>
                                    {child.registry_number ?? emptyValue}
                                </td>
                                <td className={cellClass}>
                                    {child.page_number ?? emptyValue}
                                </td>
                                <td className={cellClass}>
                                    {child.profession ?? emptyValue}
                                </td>
                                <td className={cellClass}>
                                    <div className="flex justify-center gap-1">
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            className="text-primary size-8"
                                            onClick={() => onEdit(child)}
                                            aria-label="تعديل"
                                        >
                                            <Pencil className="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            className="text-destructive size-8"
                                            onClick={() =>
                                                deleteChild(
                                                    familyId,
                                                    child,
                                                    onDeleted,
                                                )
                                            }
                                            aria-label="حذف"
                                        >
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}
