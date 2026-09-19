import { Head, Link } from '@inertiajs/react';
import { StatisticsChartsSheet } from '@/components/statistics/statistics-charts-sheet';
import { Button } from '@/components/ui/button';
import type { StatisticsChartReport } from '@/types';
import { Printer, X } from 'lucide-react';

type Props = {
    report: StatisticsChartReport;
};

function printCharts(): void {
    window.print();
}

export default function StatisticsChartsPage({ report }: Props) {
    return (
        <>
            <Head title="إحصائية بيانية" />
            <div className="flex h-full min-h-0 flex-1 flex-col gap-4 overflow-y-auto p-4 print:h-auto print:overflow-visible print:p-0">
                <div className="mx-auto flex w-full max-w-5xl justify-end gap-2 print:hidden">
                    <Button type="button" onClick={printCharts}>
                        <Printer className="size-4" />
                        طباعة
                    </Button>
                    <Button variant="outline" asChild>
                        <Link href="/dashboard">
                            <X className="size-4" />
                            اغلاق
                        </Link>
                    </Button>
                </div>
                <StatisticsChartsSheet report={report} />
            </div>
        </>
    );
}

StatisticsChartsPage.layout = {
    breadcrumbs: [],
};
