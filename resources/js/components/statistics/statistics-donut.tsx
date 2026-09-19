import { chartColor } from '@/lib/chart-palette';
import type { StatisticsCountRow } from '@/types';

type Props = {
    title: string;
    rows: StatisticsCountRow[];
};

const radius = 42;
const circumference = 2 * Math.PI * radius;

function dashFor(percent: number): number {
    return (percent / 100) * circumference;
}

export function StatisticsDonut({ title, rows }: Props) {
    const visible = rows.filter((row) => row.total > 0);
    let offset = 0;

    return (
        <section className="border-primary bg-background flex flex-col items-center gap-3 rounded-md border-2 p-4">
            <h2 className="text-center font-semibold">{title}</h2>
            <svg viewBox="0 0 120 120" className="size-40">
                <circle
                    cx="60"
                    cy="60"
                    r={radius}
                    fill="none"
                    stroke="var(--brand-cream)"
                    strokeWidth="16"
                />
                {visible.map((row, index) => {
                    const length = dashFor(row.percent);
                    const circle = (
                        <circle
                            key={row.label}
                            cx="60"
                            cy="60"
                            r={radius}
                            fill="none"
                            stroke={chartColor(index)}
                            strokeWidth="16"
                            strokeDasharray={`${length} ${circumference - length}`}
                            strokeDashoffset={-offset}
                            transform="rotate(-90 60 60)"
                        />
                    );
                    offset += length;
                    return circle;
                })}
            </svg>
            <ul className="w-full space-y-1 text-sm">
                {rows.map((row, index) => (
                    <li key={row.label} className="flex items-center justify-between gap-2">
                        <span className="flex items-center gap-2">
                            <span
                                className="size-2.5 shrink-0 rounded-full"
                                style={{ backgroundColor: chartColor(index) }}
                            />
                            {row.label}
                        </span>
                        <span className="font-medium">
                            {row.total} ({row.percent}%)
                        </span>
                    </li>
                ))}
            </ul>
        </section>
    );
}
