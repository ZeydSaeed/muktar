import { Form } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { FamilyChildRow } from '@/types';

type Props = {
    familyId: number;
    items: FamilyChildRow[];
    onEdit: (child: FamilyChildRow) => void;
};

const emptyValue = '—';

export function FamilyChildrenTable({ familyId, items, onEdit }: Props) {
    return (
        <div className="border-primary overflow-hidden rounded-lg border-2">
            <table className="w-full border-collapse text-right text-sm">
                <thead className="bg-primary text-primary-foreground">
                    <tr>
                        {[
                            'ت',
                            'اسم الابن',
                            'التولد',
                            'رقم هوية البطاقة الوطنية',
                            'السجل',
                            'الصحيفة',
                            'المهنة',
                            'إجراءات',
                        ].map((title) => (
                            <th
                                key={title}
                                className="border-primary border px-2 py-2 font-semibold"
                            >
                                {title}
                            </th>
                        ))}
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
                                <td className="border-primary border px-2 py-2">
                                    {index + 1}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.name}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.birth_date ?? emptyValue}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.national_id_number ?? emptyValue}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.registry_number ?? emptyValue}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.page_number ?? emptyValue}
                                </td>
                                <td className="border-primary border px-2 py-2">
                                    {child.profession ?? emptyValue}
                                </td>
                                <td className="border-primary border px-2 py-2">
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
                                        <Form
                                            action={`/families/${familyId}/children/${child.id}`}
                                            method="delete"
                                            options={{ preserveScroll: true }}
                                            onSubmit={(event) => {
                                                if (
                                                    !window.confirm(
                                                        `هل تريد حذف ${child.name}؟`,
                                                    )
                                                ) {
                                                    event.preventDefault();
                                                }
                                            }}
                                        >
                                            {({ processing }) => (
                                                <Button
                                                    type="submit"
                                                    size="icon"
                                                    variant="ghost"
                                                    className="text-destructive size-8"
                                                    disabled={processing}
                                                    aria-label="حذف"
                                                >
                                                    <Trash2 className="size-4" />
                                                </Button>
                                            )}
                                        </Form>
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
