import type { StatisticsKpi } from '@/types';

type Props = {
    items: StatisticsKpi[];
};

export function StatisticsKpiGrid({ items }: Props) {
    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {items.map((item) => (
                <div
                    key={item.label}
                    className="border-primary bg-background rounded-md border-2 px-3 py-3 text-center"
                >
                    <p className="text-muted-foreground text-[12px]">{item.label}</p>
                    <p className="text-primary mt-1 text-[22px] font-bold">{item.value}</p>
                </div>
            ))}
        </div>
    );
}
