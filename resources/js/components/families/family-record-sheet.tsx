import { FamilyRecordMembersTable } from '@/components/families/family-record-members-table';
import {
    RecordInline,
    RecordLine,
} from '@/components/families/record-line';
import type { FamilyChildRow } from '@/types';

export type FamilyRecordHead = {
    full_name: string;
    mother_name: string | null;
    spouse_name: string | null;
    spouse_profession: string | null;
    birth_place: string | null;
    birth_date: string | null;
    profession: string | null;
    ration_card_number: number | null;
    ration_agent_name: string | null;
    housing_card_number: number | null;
    housing_card_date: string | null;
    issuing_authority: string | null;
    current_governorate: string | null;
    current_district: string | null;
    current_subdistrict: string | null;
    current_neighborhood: string | null;
    current_alley: string | null;
    house_number: number | null;
    nearest_landmark: string | null;
    previous_governorate: string | null;
    previous_district: string | null;
    previous_subdistrict: string | null;
    previous_neighborhood: string | null;
    previous_address: string | null;
    previous_landmark: string | null;
    previous_mukhtar_name: string | null;
    karbala_residence_date: string | null;
    mobile_number: string | null;
};

export type FamilyRecord = {
    id: number;
    title: string;
    date: string | null;
    head: FamilyRecordHead;
    children: FamilyChildRow[];
};

type Props = {
    record: FamilyRecord;
};

function joinParts(...parts: Array<string | number | null | undefined>): string {
    return parts
        .map((part) => (part === null || part === undefined ? '' : String(part)))
        .filter((part) => part !== '')
        .join(' / ');
}

export function FamilyRecordSheet({ record }: Props) {
    const { head, children } = record;

    return (
        <article
            id="family-record-print"
            dir="rtl"
            className="border-primary bg-card mx-auto w-full max-w-4xl space-y-4 rounded-xl border-2 p-5 print:max-w-none print:rounded-none print:border-0 print:p-0 print:shadow-none"
        >
            <header className="relative py-2 text-center">
                <p className="text-muted-foreground absolute start-0 top-0 text-sm">
                    التاريخ : {record.date ?? '____ / ____ / ______'}
                </p>
                <h1 className="text-xl font-bold">{record.title}</h1>
            </header>

            <div className="space-y-3">
                <RecordLine
                    label="الاسم الرباعي واللقب"
                    value={head.full_name}
                />
                <RecordLine label="اسم الأم الثلاثي" value={head.mother_name} />
                <RecordLine
                    label="اسم الزوجة الثلاثي"
                    value={head.spouse_name}
                />
                <RecordLine
                    label="مهنة الزوجة"
                    value={head.spouse_profession}
                />
                <RecordLine
                    label="محل وتاريخ الولادة"
                    value={joinParts(head.birth_place, head.birth_date)}
                />
                <RecordLine
                    label="مهنة رب الأسرة الحالية"
                    value={head.profession}
                />
                <RecordLine
                    label="رقم واسم وكيل الحصة التموينية"
                    value={joinParts(
                        head.ration_card_number,
                        head.ration_agent_name,
                    )}
                />
                <div className="flex flex-wrap gap-3">
                    <RecordInline
                        label="رقم وتاريخ بطاقة السكن"
                        value={joinParts(
                            head.housing_card_number,
                            head.housing_card_date,
                        )}
                    />
                    <RecordInline
                        label="جهة الإصدار"
                        value={head.issuing_authority}
                    />
                </div>
                <div className="space-y-2">
                    <p className="text-sm font-semibold">عنوان السكن الحالي :</p>
                    <div className="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <RecordInline
                            label="المحافظة"
                            value={head.current_governorate}
                        />
                        <RecordInline
                            label="القضاء"
                            value={head.current_district}
                        />
                        <RecordInline
                            label="الناحية"
                            value={head.current_subdistrict}
                        />
                        <RecordInline
                            label="المحلة"
                            value={head.current_neighborhood}
                        />
                        <RecordInline
                            label="الزقاق"
                            value={head.current_alley}
                        />
                        <RecordInline
                            label="رقم الدار"
                            value={head.house_number}
                        />
                    </div>
                </div>
                <RecordLine
                    label="أقرب نقطة دالة للسكن الحالي"
                    value={head.nearest_landmark}
                />
                <div className="space-y-2">
                    <p className="text-sm font-semibold">عنوان السكن السابق :</p>
                    <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <RecordInline
                            label="المحافظة"
                            value={
                                head.previous_governorate ??
                                head.previous_address
                            }
                        />
                        <RecordInline
                            label="القضاء"
                            value={head.previous_district}
                        />
                        <RecordInline
                            label="الناحية"
                            value={head.previous_subdistrict}
                        />
                        <RecordInline
                            label="المحلة"
                            value={head.previous_neighborhood}
                        />
                    </div>
                </div>
                <RecordLine
                    label="أقرب نقطة دالة للسكن السابق"
                    value={head.previous_landmark}
                />
                <RecordLine
                    label="اسم المختار السابق"
                    value={head.previous_mukhtar_name}
                />
                <RecordLine
                    label="تاريخ السكن في كربلاء"
                    value={head.karbala_residence_date}
                />
                <RecordLine
                    label="رقم هاتف رب الأسرة"
                    value={head.mobile_number}
                />
            </div>

            <div className="space-y-2">
                <h2 className="text-center text-base font-bold">
                    أفراد العائلة
                </h2>
                <FamilyRecordMembersTable childrenRows={children} />
            </div>
        </article>
    );
}
