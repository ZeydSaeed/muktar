<?php

namespace App\Support;

final class FamilyExcelColumns
{
    /**
     * @return array<string, string>
     */
    public static function familyHeaders(): array
    {
        return [
            'اسم رب الاسرة' => 'head_of_family_name',
            'اسم الزوجة او الزوج' => 'spouse_name',
            'المهنة' => 'profession',
            'اسم الام' => 'mother_name',
            'رقم الدار' => 'house_number',
            'رقم البطاقة التموينية' => 'ration_card_number',
            'رقم بطاقة السكن' => 'housing_card_number',
            'رقم البطاقة الوطنية' => 'national_id_number',
            'رقم الموبايل' => 'mobile_number',
            'تاريخ السكن الحالي' => 'current_residence_date',
            'المواليد' => 'birth_date',
            'محل الولادة' => 'birth_place',
            'عدد افراد الاسرة' => 'family_members_count',
            'عدد الذكور' => 'males_count',
            'عدد الاناث' => 'females_count',
            'اسم وكيل الحصة التموينية' => 'ration_agent_name',
            'تاريخ بطاقة السكن' => 'housing_card_date',
            'جهة الإصدار' => 'issuing_authority',
            'المحافظة' => 'current_governorate',
            'القضاء' => 'current_district',
            'الناحية' => 'current_subdistrict',
            'المحلة' => 'current_neighborhood',
            'الزقاق' => 'current_alley',
            'عنوان السكن السابق' => 'previous_address',
            'المحافظة السابقة' => 'previous_governorate',
            'القضاء السابق' => 'previous_district',
            'الناحية السابقة' => 'previous_subdistrict',
            'المحلة السابقة' => 'previous_neighborhood',
            'نقطة دالة السكن السابق' => 'previous_landmark',
            'اسم المختار السابق' => 'previous_mukhtar_name',
            'تاريخ السكن في كربلاء' => 'karbala_residence_date',
            'رقم الصفحة في السجل' => 'registry_page_number',
            'اقرب نقطة دالة' => 'nearest_landmark',
            'رقم موبايل ثاني' => 'secondary_mobile_number',
            'الملاحظات' => 'notes',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function childHeaders(): array
    {
        return [
            'اسم رب الاسرة' => 'head_of_family_name',
            'رقم البطاقة الوطنية' => 'family_national_id_number',
            'اسم الابن' => 'name',
            'التولد' => 'birth_date',
            'رقم هوية البطاقة الوطنية' => 'national_id_number',
            'السجل' => 'registry_number',
            'الصحيفة' => 'page_number',
            'المهنة' => 'profession',
        ];
    }
}
