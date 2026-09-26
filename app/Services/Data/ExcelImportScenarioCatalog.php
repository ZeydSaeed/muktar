<?php

namespace App\Services\Data;

class ExcelImportScenarioCatalog
{
    public const NAME_PREFIX = 'تجربة استيراد';

    /**
     * @return list<list<array<string, string>>>
     */
    public function twentyFamilies(): array
    {
        $blocks = [];

        foreach (range(1, 8) as $number) {
            $blocks[] = $this->coupleWithChildren($number);
        }

        foreach (range(9, 12) as $number) {
            $blocks[] = $this->coupleWithoutChildren($number);
        }

        foreach (range(13, 16) as $number) {
            $blocks[] = $this->widow($number);
        }

        foreach (range(17, 20) as $number) {
            $blocks[] = $this->widower($number);
        }

        return $blocks;
    }

    /**
     * @return list<array<string, string>>
     */
    private function coupleWithChildren(int $number): array
    {
        return [
            $this->head($number, "حسن علي كاظم-{$number}", 'موظف'),
            $this->person("فاطمة محمد علي-{$number}", 'ربة بيت', '1978-03-15'),
            $this->person("أحمد حسن علي-{$number}", 'طالب', '2008-04-10'),
            $this->person("زينب حسن علي-{$number}", 'طالبة', '2012-09-01'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function coupleWithoutChildren(int $number): array
    {
        return [
            $this->head($number, "كاظم جواد محمد-{$number}", 'معلم'),
            $this->person("زينب علي حسين-{$number}", 'موظفة', '1982-06-20'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widow(int $number): array
    {
        $head = $this->head($number, "سعاد كاظم حسن-{$number}", 'ربة بيت');
        $head['اسم الزوجة او الزوج'] = 'ارملة';

        return [
            $head,
            $this->person("علي كاظم حسن-{$number}", 'طالب', '2009-01-12'),
            $this->person("مريم كاظم حسن-{$number}", 'طالبة', '2013-07-08'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function widower(int $number): array
    {
        $head = $this->head($number, "جواد حسن علي-{$number}", 'متقاعد');
        $head['اسم الزوجة او الزوج'] = 'الزوجة متوفاة';

        return [
            $head,
            $this->person("حسين جواد حسن-{$number}", 'طالب', '2007-11-03'),
            $this->person("هدى جواد حسن-{$number}", 'طالبة', '2011-02-18'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function head(int $number, string $name, string $profession): array
    {
        return [
            'اسم رب الاسرة' => $this->fullName($name),
            'المهنة' => $profession,
            'اسم الام' => "أم {$name}",
            'رقم الدار' => (string) (91000 + $number),
            'رقم البطاقة الوطنية' => 'N'.str_pad((string) $number, 6, '0', STR_PAD_LEFT),
            'رقم الموبايل' => '0770'.str_pad((string) (1000000 + $number), 7, '0', STR_PAD_LEFT),
            'المواليد' => '1970-05-12',
            'محل الولادة' => 'كربلاء',
            'عدد افراد الاسرة' => $number <= 8 ? '4' : ($number <= 12 ? '2' : '3'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function person(string $name, string $profession, string $birthDate): array
    {
        return [
            'اسم رب الاسرة' => $this->fullName($name),
            'المهنة' => $profession,
            'المواليد' => $birthDate,
        ];
    }

    private function fullName(string $name): string
    {
        return self::NAME_PREFIX.' '.$name;
    }
}
