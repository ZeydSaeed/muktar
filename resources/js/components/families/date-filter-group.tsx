import { FilterSelect } from '@/components/families/filter-select';
import type { FamilyFilterKey } from '@/lib/family-table-query';
import { visitFamilyTable } from '@/lib/family-table-query';
import type { FilterOption } from '@/types';

const dateOpOptions = [
    { value: 'before', label: 'قبل' },
    { value: 'after', label: 'بعد' },
    { value: 'between', label: 'بين' },
];

type Props = {
    opId: string;
    opLabel: string;
    opValue: string;
    dateValue: string;
    dateToValue: string;
    dates: FilterOption[];
    dateKey: FamilyFilterKey;
    dateToKey: FamilyFilterKey;
    opKey: FamilyFilterKey;
};

export function DateFilterGroup({
    opId,
    opLabel,
    opValue,
    dateValue,
    dateToValue,
    dates,
    dateKey,
    dateToKey,
    opKey,
}: Props) {
    return (
        <div className="space-y-1">
            <FilterSelect
                id={opId}
                label={opLabel}
                value={opValue}
                options={dateOpOptions}
                allLabel="—"
                onChange={(value) => visitFamilyTable({ [opKey]: value })}
            />
            <div className={opValue === 'between' ? 'grid grid-cols-2 gap-1' : ''}>
                <FilterSelect
                    id={`${opId}-from`}
                    label={opValue === 'between' ? 'من' : undefined}
                    value={dateValue}
                    options={dates}
                    allLabel="تاريخ"
                    onChange={(value) => visitFamilyTable({ [dateKey]: value })}
                />
                {opValue === 'between' ? (
                    <FilterSelect
                        id={`${opId}-to`}
                        label="إلى"
                        value={dateToValue}
                        options={dates}
                        allLabel="تاريخ"
                        onChange={(value) =>
                            visitFamilyTable({ [dateToKey]: value })
                        }
                    />
                ) : null}
            </div>
        </div>
    );
}
