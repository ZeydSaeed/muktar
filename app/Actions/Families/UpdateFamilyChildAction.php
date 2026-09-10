<?php

namespace App\Actions\Families;

use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;

class UpdateFamilyChildAction
{
    public function __construct(
        private FamilyChildRepositoryInterface $children,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function execute(FamilyChild $child, array $attributes): FamilyChild
    {
        return $this->children->update($child, $attributes);
    }
}
