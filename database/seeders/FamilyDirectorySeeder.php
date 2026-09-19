<?php

namespace Database\Seeders;

use App\Actions\Families\StoreFamilyAction;
use App\Actions\Families\StoreFamilyChildAction;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use Database\Seeders\Builders\FamilyDirectoryBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilyDirectorySeeder extends Seeder
{
    public function __construct(
        private FamilyDirectoryBuilder $builder,
        private FamilyRepositoryInterface $families,
        private FamilyChildRepositoryInterface $children,
        private StoreFamilyAction $storeFamily,
        private StoreFamilyChildAction $storeChild,
    ) {}

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->children->deleteAll();
            $this->families->deleteAll();

            foreach ($this->builder->build() as $row) {
                $family = $this->storeFamily->execute($row['family']);

                foreach ($row['children'] as $child) {
                    $this->storeChild->execute($family, $child);
                }
            }
        });
    }
}
