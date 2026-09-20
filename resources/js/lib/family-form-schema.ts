export type FamilyFormFieldType = 'text' | 'number' | 'date';

export type FamilyFormFieldDef = {
    name: string;
    label: string;
    type?: FamilyFormFieldType;
    required?: boolean;
    span?: 'wide' | 'full';
};

export type FamilyFormSectionDef = {
    title: string;
    fields: FamilyFormFieldDef[];
};

export const familyFormSections: FamilyFormSectionDef[] = [
    {
        title: 'بيانات رب الأسرة',
        fields: [
            {
                name: 'head_of_family_name',
                label: 'اسم رب الاسرة الرباعي واللقب',
                required: true,
                span: 'wide',
            },
            { name: 'mother_name', label: 'اسم الام الثلاثي' },
            { name: 'spouse_name', label: 'اسم الزوجة الثلاثي' },
            { name: 'spouse_profession', label: 'مهنة الزوجة' },
            { name: 'profession', label: 'مهنة رب الاسرة' },
            { name: 'birth_date', label: 'المواليد', type: 'date' },
            { name: 'birth_place', label: 'محل الولادة' },
        ],
    },
    {
        title: 'الوثائق الرسمية',
        fields: [
            { name: 'national_id_number', label: 'رقم البطاقة الوطنية' },
            {
                name: 'ration_card_number',
                label: 'رقم البطاقة التموينية',
                type: 'number',
            },
            { name: 'ration_agent_name', label: 'اسم وكيل الحصة التموينية' },
            {
                name: 'housing_card_number',
                label: 'رقم بطاقة السكن',
                type: 'number',
            },
            {
                name: 'housing_card_date',
                label: 'تاريخ بطاقة السكن',
                type: 'date',
            },
            { name: 'issuing_authority', label: 'جهة الإصدار' },
        ],
    },
    {
        title: 'أرقام الاتصال',
        fields: [
            { name: 'mobile_number', label: 'رقم الموبايل' },
            { name: 'secondary_mobile_number', label: 'رقم موبايل ثاني' },
        ],
    },
    {
        title: 'أفراد الأسرة',
        fields: [
            {
                name: 'family_members_count',
                label: 'عدد افراد الاسرة',
                type: 'number',
            },
            { name: 'males_count', label: 'عدد الذكور', type: 'number' },
            { name: 'females_count', label: 'عدد الاناث', type: 'number' },
        ],
    },
    {
        title: 'عنوان السكن الحالي',
        fields: [
            { name: 'current_governorate', label: 'المحافظة' },
            { name: 'current_district', label: 'القضاء' },
            { name: 'current_subdistrict', label: 'الناحية' },
            { name: 'current_neighborhood', label: 'المحلة' },
            { name: 'current_alley', label: 'الزقاق' },
            { name: 'house_number', label: 'رقم الدار', type: 'number' },
            {
                name: 'nearest_landmark',
                label: 'اقرب نقطة دالة',
                span: 'wide',
            },
            {
                name: 'current_residence_date',
                label: 'تاريخ السكن الحالي',
                type: 'date',
            },
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
        ],
    },
    {
        title: 'عنوان السكن السابق',
        fields: [
            { name: 'previous_governorate', label: 'المحافظة السابقة' },
            { name: 'previous_district', label: 'القضاء السابق' },
            { name: 'previous_subdistrict', label: 'الناحية السابقة' },
            { name: 'previous_neighborhood', label: 'المحلة السابقة' },
            {
                name: 'previous_address',
                label: 'عنوان السكن السابق',
                span: 'wide',
            },
            {
                name: 'previous_landmark',
                label: 'نقطة دالة السكن السابق',
                span: 'wide',
            },
            { name: 'previous_mukhtar_name', label: 'اسم المختار السابق' },
        ],
    },
];
