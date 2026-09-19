import { Head, Link } from '@inertiajs/react';
import {
    FamilyRecordSheet,
    type FamilyRecord,
} from '@/components/families/family-record-sheet';
import { Button } from '@/components/ui/button';
import { Printer, X } from 'lucide-react';

type Props = {
    record: FamilyRecord;
};

function printFamilyRecord(): void {
    window.print();
}

export default function FamilyRecordPage({ record }: Props) {
    return (
        <>
            <Head title="سجل العائلة" />
            <div className="flex h-full min-h-0 flex-1 flex-col gap-4 overflow-y-auto p-4 print:h-auto print:overflow-visible print:p-0">
                <div className="mx-auto flex w-full max-w-4xl justify-end gap-2 print:hidden">
                    <Button type="button" onClick={printFamilyRecord}>
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
                <FamilyRecordSheet record={record} />
            </div>
        </>
    );
}

FamilyRecordPage.layout = {
    breadcrumbs: [],
};
