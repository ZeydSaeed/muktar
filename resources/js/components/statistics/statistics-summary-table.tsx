import type { StatisticsSummary } from '@/types';

const summaryItems: Array<{ key: keyof StatisticsSummary; label: string }> = [
    { key: 'families', label: 'عدد العوائل' },
    { key: 'children', label: 'عدد الأولاد' },
    { key: 'people', label: 'مجموع الأفراد (رب الأسرة + الأولاد)' },
    { key: 'families_with_children', label: 'عوائل لديها أولاد' },
    { key: 'families_without_children', label: 'عوائل بلا أولاد' },
    { key: 'average_children', label: 'معدل الأولاد لكل عائلة' },
    { key: 'males', label: 'مجموع الذكور المسجّل' },
    { key: 'females', label: 'مجموع الإناث المسجّل' },
    { key: 'recorded_members', label: 'مجموع أفراد العائلة المسجّل' },
    { key: 'missing_mobile', label: 'بلا رقم موبايل' },
    { key: 'missing_national_id', label: 'بلا هوية وطنية' },
    { key: 'missing_profession', label: 'بلا مهنة' },
    { key: 'missing_house_number', label: 'بلا رقم دار' },
    { key: 'missing_ration_card', label: 'بلا بطاقة تموينية' },
    { key: 'missing_housing_card', label: 'بلا بطاقة سكن' },
];

type Props = {
    summary: StatisticsSummary;
};

export function StatisticsSummaryTable({ summary }: Props) {
    return (
        <table className="w-full border-collapse text-center text-sm">
            <thead className="bg-primary text-primary-foreground">
                <tr>
                    <th className="border-primary border px-3 py-2 text-center align-middle font-semibold">
                        البيان
                    </th>
                    <th className="border-primary w-32 border px-3 py-2 text-center align-middle font-semibold">
                        العدد
                    </th>
                </tr>
            </thead>
            <tbody>
                {summaryItems.map((item) => (
                    <tr key={item.key} className="even:bg-muted/40">
                        <td className="border-primary border px-3 py-1.5 text-center align-middle">
                            {item.label}
                        </td>
                        <td className="border-primary border px-3 py-1.5 text-center align-middle font-medium">
                            {summary[item.key]}
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
