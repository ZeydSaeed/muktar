<?php

namespace App\Services\Data;

class ExcelTemplateReferenceCatalog
{
    /**
     * @return list<list<array<string, string>>>
     */
    public function families(): array
    {
        return [
            $this->coupleWithChildren(),
            $this->coupleWithoutChildren(),
            $this->widowWithChildren('سعاد كاظم حسن', 'ارملة', '33010', 'علي كاظم حسن', 'مريم كاظم حسن'),
            $this->widowWithChildren('حليمة جابر موسى', 'ارمله', '33020', 'رقية علي جابر', 'جعفر علي جابر'),
            $this->widowAlone(),
            $this->widowerWithChildren('جواد حسن علي', 'الزوجة متوفاة', '34020', 'حسين جواد حسن', 'هدى جواد حسن'),
            $this->widowerWithChildren('عباس جبار سلطان', 'الزوجه متوفاه', '34030', 'سجاد عباس جبار', 'زهراء عباس جبار'),
            $this->widowerWithChildren('منصور شلج مرشد', 'الزوجة متوفاه', '34040', 'مهدي منصور شلج', 'فاطمة منصور شلج'),
            $this->widowerAlone(),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function coupleWithChildren(): array
    {
        return [
            $this->completeHead('حسن علي كاظم الجواد', 'موظف', 'مثال: أب ثم زوجة ثم أبناء ثم صف فارغ'),
            $this->member('فاطمة محمد علي', 'ربة بيت', '1975-02-20', 'A7654321'),
            $this->member('أحمد حسن علي', 'طالب', '2008-04-10', 'B1111111'),
            $this->member('زينب حسن علي', 'طالبة', '2012-09-01', 'B2222222'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function coupleWithoutChildren(): array
    {
        return [
            $this->head('كاظم جواد محمد', 'معلم', '32045', 'مثال: أب وزوجة بلا أبناء ثم صف فارغ'),
            $this->member('زينب علي حسين', 'موظفة', '1982-06-20', 'C3333333'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widowWithChildren(
        string $name,
        string $marker,
        string $house,
        string $son,
        string $daughter,
    ): array {
        return [
            $this->statusHead($name, 'ربة بيت', $marker, $house, 'مثال: اسم الأرملة في اسم رب الاسرة ثم الأبناء'),
            $this->member($son, 'طالب', '2009-01-12'),
            $this->member($daughter, 'طالبة', '2013-07-08'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widowAlone(): array
    {
        return [
            $this->statusHead('ناهد ناصر كامل', 'معلمة', 'ارملة', '33030', 'مثال: أرملة بلا أبناء ثم صف فارغ'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widowerWithChildren(
        string $name,
        string $marker,
        string $house,
        string $son,
        string $daughter,
    ): array {
        return [
            $this->statusHead($name, 'متقاعد', $marker, $house, 'مثال: اسم الزوج في اسم رب الاسرة ثم الأبناء'),
            $this->member($son, 'طالب', '2007-11-03'),
            $this->member($daughter, 'طالبة', '2011-02-18'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widowerAlone(): array
    {
        return [
            $this->statusHead('غالب حبيب جواد', 'كاسب', 'الزوجة متوفاة', '34050', 'مثال: زوجة متوفاة بلا أبناء ثم صف فارغ'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function completeHead(string $name, string $profession, string $note): array
    {
        return array_merge(
            $this->head($name, $profession, '31012', $note),
            $this->identityFields(),
            $this->addressFields(),
        );
    }

    /**
     * @return array<string, string>
     */
    private function identityFields(): array
    {
        return [
            'اسم الام' => 'زينب حسن كاظم',
            'مهنة الزوجة' => 'ربة بيت',
            'رقم البطاقة التموينية' => '138597',
            'رقم بطاقة السكن' => '1464048',
            'رقم البطاقة الوطنية' => 'A1234567',
            'رقم الموبايل' => '07701234567',
            'تاريخ السكن الحالي' => '2010-06-01',
            'المواليد' => '1970-05-12',
            'محل الولادة' => 'كربلاء',
            'عدد افراد الاسرة' => '4',
            'عدد الذكور' => '2',
            'عدد الاناث' => '2',
            'اسم وكيل الحصة التموينية' => 'وكيل الحي',
            'رقم موبايل ثاني' => '07801234567',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function addressFields(): array
    {
        return [
            'تاريخ بطاقة السكن' => '2018-03-15',
            'جهة الإصدار' => 'دائرة الجنسية',
            'المحافظة' => 'كربلاء',
            'القضاء' => 'مركز كربلاء',
            'الناحية' => 'المركز',
            'المحلة' => 'حي الإسكان العسكري',
            'الزقاق' => '3',
            'عنوان السكن السابق' => 'الحلة',
            'المحافظة السابقة' => 'بابل',
            'القضاء السابق' => 'الحلة',
            'الناحية السابقة' => 'المركز',
            'المحلة السابقة' => 'الجمعية',
            'نقطة دالة السكن السابق' => 'قرب السوق',
            'اسم المختار السابق' => 'عبد الحسين كاظم',
            'تاريخ السكن في كربلاء' => '2010-06-01',
            'رقم الصفحة في السجل' => '12',
            'اقرب نقطة دالة' => 'الحزام الأخضر',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function head(string $name, string $profession, string $house, string $note): array
    {
        return [
            'اسم رب الاسرة' => $name,
            'المهنة' => $profession,
            'رقم الدار' => $house,
            'محل الولادة' => 'كربلاء',
            'الملاحظات' => $note,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusHead(
        string $name,
        string $profession,
        string $marker,
        string $house,
        string $note,
    ): array {
        $row = $this->head($name, $profession, $house, $note);
        $row['اسم الزوجة او الزوج'] = $marker;

        return $row;
    }

    /**
     * @return array<string, string>
     */
    private function member(string $name, string $profession, string $birth, string $nationalId = ''): array
    {
        $row = [
            'اسم رب الاسرة' => $name,
            'المهنة' => $profession,
            'المواليد' => $birth,
        ];

        if ($nationalId !== '') {
            $row['رقم البطاقة الوطنية'] = $nationalId;
        }

        return $row;
    }
}
