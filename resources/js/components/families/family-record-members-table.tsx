import type { FamilyChildRow } from '@/types';

type Props = {
    childrenRows: Array<FamilyChildRow | null>;
};

export function FamilyRecordMembersTable({ childrenRows }: Props) {
    return (
        <div className="overflow-hidden rounded-md border-2 border-primary">
            <table className="w-full border-collapse text-right text-sm">
                <thead className="bg-primary text-primary-foreground">
                    <tr>
                        {[
                            'ت',
                            'الاسم',
                            'التولد',
                            'رقم هوية الأحوال المدنية',
                            'السجل',
                            'الصحيفة',
                            'المهنة',
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
                    {childrenRows.map((child, index) => (
                        <tr key={child?.id ?? `empty-${index}`}>
                            <td className="border-primary border px-2 py-2">
                                {index + 1}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.name ?? ''}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.birth_date ?? ''}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.national_id_number ?? ''}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.registry_number ?? ''}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.page_number ?? ''}
                            </td>
                            <td className="border-primary border px-2 py-2">
                                {child?.profession ?? ''}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
