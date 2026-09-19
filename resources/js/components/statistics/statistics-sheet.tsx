import { StatisticsBreakdownTable } from '@/components/statistics/statistics-breakdown-table';
import { StatisticsSummaryTable } from '@/components/statistics/statistics-summary-table';
import type { StatisticsReport } from '@/types';

type Props = {
    report: StatisticsReport;
};

export function StatisticsSheet({ report }: Props) {
    return (
        <article
            dir="rtl"
            className="border-primary bg-card mx-auto w-full max-w-4xl space-y-6 rounded-xl border-2 p-5 print:max-w-none print:rounded-none print:border-0 print:p-0"
        >
            <header className="relative border-b-2 border-primary pb-3 text-center">
                <p className="text-muted-foreground absolute start-0 top-0 text-sm">
                    التاريخ : {report.generated_at}
                </p>
                <h1 className="text-xl font-bold">{report.title}</h1>
                <p className="text-muted-foreground mt-1 text-sm">
                    تقرير إحصائي مفصل عن العوائل والأولاد المسجّلين
                </p>
            </header>

            <StatisticsSummaryTable summary={report.summary} />

            {report.sections.map((section) => (
                <StatisticsBreakdownTable
                    key={section.title}
                    title={section.title}
                    rows={section.rows}
                />
            ))}
        </article>
    );
}
