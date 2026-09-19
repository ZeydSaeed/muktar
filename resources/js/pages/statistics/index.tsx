import { Head, Link } from '@inertiajs/react';
import { StatisticsSheet } from '@/components/statistics/statistics-sheet';
import { Button } from '@/components/ui/button';
import type { StatisticsReport } from '@/types';
import { Printer, X } from 'lucide-react';

type Props = {
    report: StatisticsReport;
};

function printStatistics(): void {
    window.print();
}

export default function StatisticsPage({ report }: Props) {
    return (
        <>
            <Head title="إحصائية" />
            <div className="flex h-full min-h-0 flex-1 flex-col gap-4 overflow-y-auto p-4 print:h-auto print:overflow-visible print:p-0">
                <div className="mx-auto flex w-full max-w-4xl justify-end gap-2 print:hidden">
                    <Button type="button" onClick={printStatistics}>
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
                <StatisticsSheet report={report} />
            </div>
        </>
    );
}

StatisticsPage.layout = {
    breadcrumbs: [],
};
