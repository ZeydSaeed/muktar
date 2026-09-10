import { Head, Link } from '@inertiajs/react';
import {
    FamilyRecordSheet,
    type FamilyRecord,
} from '@/components/families/family-record-sheet';
import { Button } from '@/components/ui/button';
import { X } from 'lucide-react';

type Props = {
    record: FamilyRecord;
};

export default function FamilyRecordPage({ record }: Props) {
    return (
        <>
            <Head title="سجل العائلة" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-y-auto p-4">
                <div className="mx-auto flex w-full max-w-4xl justify-end">
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
