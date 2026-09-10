<?php

namespace App\Actions\Families;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;

class StoreFamilyChildAction
{
    public function __construct(
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function execute(Family $family, array $attributes): FamilyChild
    {
        return $this->children->create($family, $attributes);
    }
}
