import { HighlightedName } from '@/components/families/highlighted-name';
import { Checkbox } from '@/components/ui/checkbox';
import type { FamilyTableRow } from '@/types';

type Props = {
    family: FamilyTableRow;
    sequence: number;
    search: string;
    checked: boolean;
    onToggle: (id: number) => void;
};

const emptyValue = '—';
const cellClass = 'border-primary border px-2 py-1 text-center align-middle';

function display(value: string | number | null): string {
    if (value === null || value === '') {
        return emptyValue;
    }

    return String(value);
}

export function FamilyDeletionRow({
    family,
    sequence,
    search,
    checked,
    onToggle,
}: Props) {
    return (
        <tr className="even:bg-muted/40">
            <td className={`${cellClass} text-center`}>
                <Checkbox
                    checked={checked}
                    onCheckedChange={() => onToggle(family.id)}
                    aria-label={`تحديد ${family.head_of_family_name}`}
                />
            </td>
            <td className={`${cellClass} text-center`}>{sequence}</td>
            <td className={`${cellClass} font-medium`}>
                <HighlightedName name={family.head_of_family_name} query={search} />
            </td>
            <td className={`${cellClass} text-center`}>
                {display(family.house_number)}
            </td>
            <td className={`${cellClass} text-center`} dir="ltr">
                {display(family.mobile_number)}
            </td>
            <td className={`${cellClass} text-center`}>
                {display(family.ration_card_number)}
            </td>
        </tr>
    );
}
