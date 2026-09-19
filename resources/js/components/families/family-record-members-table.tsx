import type { FamilyChildRow } from '@/types';

type Props = {
    childrenRows: FamilyChildRow[];
};

const headers = [
    'ت',
    'الاسم',
    'التولد',
    'رقم هوية الأحوال المدنية',
    'السجل',
    'الصحيفة',
    'المهنة',
] as const;

function display(value: string | number | null | undefined): string {
    return value === null || value === undefined ? '' : String(value);
}

function memberValues(child: FamilyChildRow, index: number): Array<string | number> {
    return [
        index + 1,
        child.name,
        display(child.birth_date),
        display(child.national_id_number),
        display(child.registry_number),
        display(child.page_number),
        display(child.profession),
    ];
}

function MemberRow({ child, index }: { child: FamilyChildRow; index: number }) {
    return (
        <tr>
            {memberValues(child, index).map((value, cellIndex) => (
                <td key={`${child.id}-${cellIndex}`} className="border-primary border px-2 py-2">
                    {value}
                </td>
            ))}
        </tr>
    );
}

export function FamilyRecordMembersTable({ childrenRows }: Props) {
    return (
        <div className="overflow-hidden rounded-md border-2 border-primary">
            <table className="w-full border-collapse text-right text-sm">
                <thead className="bg-primary text-primary-foreground">
                    <tr>
                        {headers.map((title) => (
                            <th
                                key={title}
                                className="border-primary border px-2 py-2 text-center font-semibold"
                            >
                                {title}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {childrenRows.map((child, index) => (
                        <MemberRow key={child.id} child={child} index={index} />
                    ))}
                </tbody>
            </table>
        </div>
    );
}
