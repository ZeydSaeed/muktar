<?php

namespace Database\Seeders\Builders;

use Database\Seeders\Data\FamilyDirectoryCatalog;
use Database\Seeders\Support\UniqueColumnGuard;

final class FamilyDirectoryBuilder
{
    public const COUNT = 100;

    public function __construct(
        private UniqueColumnGuard $guard = new UniqueColumnGuard,
    ) {}

    /**
     * @return list<array{family: array<string, mixed>, children: list<array<string, mixed>>}>
     */
    public function build(): array
    {
        $rows = [];

        for ($index = 0; $index < self::COUNT; $index++) {
            $rows[] = $this->one($index);
        }

        $this->assertUniqueFamilies($rows);
        $this->assertUniqueChildren($rows);

        return $rows;
    }

    /**
     * @return array{family: array<string, mixed>, children: list<array<string, mixed>>}
     */
    private function one(int $index): array
    {
        $children = $this->childrenFor($index);

        return [
            'family' => array_merge(
                $this->identity($index),
                $this->cards($index),
                $this->currentAddress($index),
                $this->previousAddress($index),
                $this->counts($children),
            ),
            'children' => array_map(
                static function (array $child): array {
                    unset($child['gender']);

                    return $child;
                },
                $children,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function identity(int $index): array
    {
        return [
            'head_of_family_name' => $this->maleName($index, 0),
            'spouse_name' => $this->femaleName($index, 13),
            'spouse_profession' => FamilyDirectoryCatalog::professions()[($index + 17) % 100],
            'profession' => FamilyDirectoryCatalog::professions()[$index],
            'mother_name' => $this->femaleName($index, 29),
            'birth_date' => $this->date('1958-03-01', $index * 47),
            'birth_place' => FamilyDirectoryCatalog::birthPlaces()[$index],
            'notes' => 'ملف عائلة حي الإسكان العسكري تسلسل '.($index + 1),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cards(int $index): array
    {
        return [
            'house_number' => 31000 + ($index % 10) * 1000 + $index,
            'ration_card_number' => 850001 + $index,
            'housing_card_number' => 420001 + $index,
            'national_id_number' => (string) (100000000001 + $index),
            'mobile_number' => sprintf('077%08d', 10000000 + $index),
            'secondary_mobile_number' => sprintf('078%08d', 20000000 + $index),
            'ration_agent_name' => $this->maleName($index, 11),
            'housing_card_date' => $this->date('2012-02-01', $index * 3),
            'issuing_authority' => 'مديرية الأحوال المدنية / '.FamilyDirectoryCatalog::birthPlaces()[$index],
            'registry_page_number' => 21 + $index,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function currentAddress(int $index): array
    {
        $seq = $index + 1;

        return [
            'current_residence_date' => $this->date('2010-01-01', $index),
            'karbala_residence_date' => $this->date('2004-05-01', $index * 5),
            'current_governorate' => 'كربلاء - قطاع '.$seq,
            'current_district' => 'مركز كربلاء نقطة '.$seq,
            'current_subdistrict' => 'ناحية الإسكان العسكري '.$seq,
            'current_neighborhood' => 'محلة العسكر '.$seq,
            'current_alley' => 'زقاق الرافدين '.$seq,
            'nearest_landmark' => 'جامع الإمام الحسين فرع '.$seq,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function previousAddress(int $index): array
    {
        $seq = $index + 1;

        return [
            'previous_address' => 'محلة قديمة رقم '.$seq,
            'previous_governorate' => 'محافظة سابقة '.$seq,
            'previous_district' => 'قضاء سابق '.$seq,
            'previous_subdistrict' => 'ناحية سابقة '.$seq,
            'previous_neighborhood' => 'محلة سابقة '.$seq,
            'previous_landmark' => 'سوق قديم رقم '.$seq,
            'previous_mukhtar_name' => $this->maleName($index, 19),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $children
     * @return array<string, int>
     */
    private function counts(array $children): array
    {
        $maleChildren = count(array_filter(
            $children,
            static fn (array $child): bool => $child['gender'] === 'male',
        ));

        return [
            'family_members_count' => 2 + count($children),
            'males_count' => 1 + $maleChildren,
            'females_count' => 1 + count($children) - $maleChildren,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function childrenFor(int $familyIndex): array
    {
        $count = 2 + ($familyIndex % 3);
        $rows = [];

        for ($offset = 0; $offset < $count; $offset++) {
            $global = ($familyIndex * 4) + $offset;
            $male = $offset % 2 === 0;
            $rows[] = [
                'gender' => $male ? 'male' : 'female',
                'name' => implode(' ', [
                    $male
                        ? $this->pick(FamilyDirectoryCatalog::maleFirst(), $global, 2)
                        : $this->pick(FamilyDirectoryCatalog::femaleFirst(), $global, 2),
                    FamilyDirectoryCatalog::surnames()[$familyIndex],
                    'فرع',
                    (string) ($offset + 1),
                ]),
                'birth_date' => $this->date('1998-01-15', $global * 19),
                'national_id_number' => (string) (200000000001 + $global),
                'registry_number' => 3001 + $global,
                'page_number' => 4001 + $global,
                'profession' => FamilyDirectoryCatalog::childProfessions()[$global % 20]
                    .' دفعة '.($global + 1),
            ];
        }

        return $rows;
    }

    private function maleName(int $index, int $shift): string
    {
        return implode(' ', [
            $this->pick(FamilyDirectoryCatalog::maleFirst(), $index, 1 + $shift),
            $this->pick(FamilyDirectoryCatalog::maleSecond(), $index, 3 + $shift),
            $this->pick(FamilyDirectoryCatalog::maleThird(), $index, 7 + $shift),
            FamilyDirectoryCatalog::surnames()[($index + $shift) % 100],
        ]);
    }

    private function femaleName(int $index, int $shift): string
    {
        return implode(' ', [
            $this->pick(FamilyDirectoryCatalog::femaleFirst(), $index, $shift),
            $this->pick(FamilyDirectoryCatalog::maleSecond(), $index, $shift + 2),
            $this->pick(FamilyDirectoryCatalog::maleThird(), $index, $shift + 5),
            FamilyDirectoryCatalog::surnames()[($index + $shift) % 100],
        ]);
    }

    /**
     * @param  list<string>  $items
     */
    private function pick(array $items, int $index, int $multiplier): string
    {
        return $items[($index * $multiplier) % count($items)];
    }

    private function date(string $start, int $days): string
    {
        return date('Y-m-d', strtotime($start.' +'.$days.' days'));
    }

    /**
     * @param  list<array{family: array<string, mixed>, children: list<array<string, mixed>>}>  $rows
     */
    private function assertUniqueFamilies(array $rows): void
    {
        $families = array_column($rows, 'family');
        $skip = ['family_members_count', 'males_count', 'females_count'];

        foreach (array_keys($families[0]) as $key) {
            if (in_array($key, $skip, true)) {
                continue;
            }

            $this->guard->assert($key, array_column($families, $key));
        }
    }

    /**
     * @param  list<array{family: array<string, mixed>, children: list<array<string, mixed>>}>  $rows
     */
    private function assertUniqueChildren(array $rows): void
    {
        $children = array_merge(...array_column($rows, 'children'));
        $keys = ['name', 'birth_date', 'national_id_number', 'registry_number', 'page_number', 'profession'];

        foreach ($keys as $key) {
            $this->guard->assert('child.'.$key, array_column($children, $key));
        }
    }
}
