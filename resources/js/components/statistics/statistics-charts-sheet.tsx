import { StatisticsBarChart } from '@/components/statistics/statistics-bar-chart';
import { StatisticsDonut } from '@/components/statistics/statistics-donut';
import { StatisticsKpiGrid } from '@/components/statistics/statistics-kpi-grid';
import type { StatisticsChartReport } from '@/types';

type Props = {
    report: StatisticsChartReport;
};

export function StatisticsChartsSheet({ report }: Props) {
    return (
        <article
            dir="rtl"
            className="border-primary bg-card mx-auto w-full max-w-5xl space-y-6 rounded-xl border-2 p-5 print:max-w-none print:rounded-none print:border-0 print:p-0"
        >
            <header className="relative border-b-2 border-primary pb-3 text-center">
                <p className="text-muted-foreground absolute start-0 top-0 text-sm">
                    التاريخ : {report.generated_at}
                </p>
                <h1 className="text-xl font-bold">{report.title}</h1>
                <p className="text-muted-foreground mt-1 text-sm">
                    تمثيل صوري مرتب للمخططات والهياكل الإحصائية
                </p>
            </header>

            <StatisticsKpiGrid items={report.kpis} />

            <div className="grid gap-4 md:grid-cols-3">
                {report.compositions.map((chart) => (
                    <StatisticsDonut
                        key={chart.title}
                        title={chart.title}
                        rows={chart.rows}
                    />
                ))}
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                {report.bars.map((chart) => (
                    <StatisticsBarChart
                        key={chart.title}
                        title={chart.title}
                        rows={chart.rows}
                    />
                ))}
            </div>
        </article>
    );
}
