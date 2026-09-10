import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Field = {
    name: string;
    label: string;
    type?: string;
    required?: boolean;
};

const fields: Field[] = [
    { name: 'head_of_family_name', label: 'اسم رب الاسرة', required: true },
    { name: 'spouse_name', label: 'اسم الزوجة او الزوج' },
    { name: 'profession', label: 'المهنة' },
    { name: 'mother_name', label: 'اسم الام' },
    { name: 'house_number', label: 'رقم الدار', type: 'number' },
    {
        name: 'ration_card_number',
        label: 'رقم البطاقة التموينية',
        type: 'number',
    },
    { name: 'housing_card_number', label: 'رقم بطاقة السكن', type: 'number' },
    { name: 'national_id_number', label: 'رقم البطاقة الوطنية' },
    { name: 'mobile_number', label: 'رقم الموبايل' },
    {
        name: 'current_residence_date',
        label: 'تاريخ السكن الحالي',
        type: 'date',
    },
    { name: 'birth_date', label: 'المواليد', type: 'date' },
    { name: 'birth_place', label: 'محل الولادة' },
    { name: 'family_members_count', label: 'عدد افراد الاسرة', type: 'number' },
    { name: 'males_count', label: 'عدد الذكور', type: 'number' },
    { name: 'females_count', label: 'عدد الاناث', type: 'number' },
    { name: 'ration_agent_name', label: 'اسم وكيل الحصة التموينية' },
    { name: 'housing_card_date', label: 'تاريخ بطاقة السكن', type: 'date' },
    { name: 'issuing_authority', label: 'جهة الإصدار' },
    { name: 'current_governorate', label: 'المحافظة' },
    { name: 'current_district', label: 'القضاء' },
    { name: 'current_subdistrict', label: 'الناحية' },
    { name: 'current_neighborhood', label: 'المحلة' },
    { name: 'current_alley', label: 'الزقاق' },
    { name: 'previous_address', label: 'عنوان السكن السابق' },
    { name: 'previous_governorate', label: 'المحافظة السابقة' },
    { name: 'previous_district', label: 'القضاء السابق' },
    { name: 'previous_subdistrict', label: 'الناحية السابقة' },
    { name: 'previous_neighborhood', label: 'المحلة السابقة' },
    { name: 'previous_landmark', label: 'نقطة دالة السكن السابق' },
    { name: 'previous_mukhtar_name', label: 'اسم المختار السابق' },
    {
        name: 'karbala_residence_date',
        label: 'تاريخ السكن في كربلاء',
        type: 'date',
    },
    {
        name: 'registry_page_number',
        label: 'رقم الصفحة في السجل',
        type: 'number',
    },
    { name: 'nearest_landmark', label: 'اقرب نقطة دالة' },
    { name: 'secondary_mobile_number', label: 'رقم موبايل ثاني' },
];

type Props = {
    errors: Record<string, string>;
    values?: Record<string, string | number | null>;
};

function fieldValue(
    values: Record<string, string | number | null> | undefined,
    name: string,
): string {
    const value = values?.[name];

    if (value === null || value === undefined) {
        return '';
    }

    return String(value);
}

export function FamilyFormFields({ errors, values }: Props) {
    return (
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {fields.map((field) => (
                <div key={field.name} className="grid gap-1.5">
                    <Label htmlFor={field.name}>{field.label}</Label>
                    <Input
                        id={field.name}
                        name={field.name}
                        type={field.type ?? 'text'}
                        required={field.required}
                        defaultValue={fieldValue(values, field.name)}
                    />
                    <InputError message={errors[field.name]} />
                </div>
            ))}

            <div className="grid gap-1.5 sm:col-span-2 lg:col-span-4">
                <Label htmlFor="notes">الملاحظات</Label>
                <textarea
                    id="notes"
                    name="notes"
                    rows={3}
                    defaultValue={fieldValue(values, 'notes')}
                    className="border-input focus-visible:border-ring focus-visible:ring-ring/50 min-h-20 w-full rounded-md border bg-transparent px-3 py-2 text-sm outline-none focus-visible:ring-[3px]"
                />
                <InputError message={errors.notes} />
            </div>
        </div>
    );
}
