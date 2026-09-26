import type { StatisticsCountRow } from '@/types';

type Props = {
    title: string;
    rows: StatisticsCountRow[];
};

export function StatisticsBreakdownTable({ title, rows }: Props) {
    return (
        <section className="space-y-2">
            <h2 className="text-center text-base font-semibold">{title}</h2>
            <table className="w-full border-collapse text-center text-sm">
                <thead className="bg-primary text-primary-foreground">
                    <tr>
                        <th className="border-primary w-12 border px-2 py-2 text-center align-middle font-semibold">
                            ت
                        </th>
                        <th className="border-primary border px-3 py-2 text-center align-middle font-semibold">
                            التفصيل
                        </th>
                        <th className="border-primary w-24 border px-2 py-2 text-center align-middle font-semibold">
                            العدد
                        </th>
                        <th className="border-primary w-24 border px-2 py-2 text-center align-middle font-semibold">
                            النسبة
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {rows.length === 0 ? (
                        <tr>
                            <td
                                colSpan={4}
                                className="border-primary text-muted-foreground border px-3 py-4 text-center"
                            >
                                لا توجد بيانات
                            </td>
                        </tr>
                    ) : (
                        rows.map((row, index) => (
                            <tr key={`${row.label}-${index}`} className="even:bg-muted/40">
                                <td className="border-primary border px-2 py-1.5 text-center align-middle">
                                    {index + 1}
                                </td>
                                <td className="border-primary border px-3 py-1.5 text-center align-middle">
                                    {row.label}
                                </td>
                                <td className="border-primary border px-2 py-1.5 text-center align-middle">
                                    {row.total}
                                </td>
                                <td className="border-primary border px-2 py-1.5 text-center align-middle">
                                    {row.percent}%
                                </td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </section>
    );
}
