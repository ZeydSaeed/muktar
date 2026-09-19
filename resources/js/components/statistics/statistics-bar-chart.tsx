import { chartColor } from '@/lib/chart-palette';
import type { StatisticsCountRow } from '@/types';

type Props = {
    title: string;
    rows: StatisticsCountRow[];
};

export function StatisticsBarChart({ title, rows }: Props) {
    const max = Math.max(...rows.map((row) => row.total), 1);

    return (
        <section className="border-primary space-y-3 rounded-md border-2 p-4">
            <h2 className="text-center font-semibold">{title}</h2>
            <div className="space-y-2">
                {rows.map((row, index) => (
                    <div key={row.label} className="grid grid-cols-[7rem_1fr_4.5rem] items-center gap-2">
                        <p className="truncate text-start text-sm" title={row.label}>
                            {row.label}
                        </p>
                        <div className="bg-muted h-3 overflow-hidden rounded-sm">
                            <div
                                className="h-full rounded-sm"
                                style={{
                                    width: `${(row.total / max) * 100}%`,
                                    backgroundColor: chartColor(index),
                                }}
                            />
                        </div>
                        <p className="text-end text-sm font-medium">
                            {row.total}
                            <span className="text-muted-foreground"> ({row.percent}%)</span>
                        </p>
                    </div>
                ))}
            </div>
        </section>
    );
}
