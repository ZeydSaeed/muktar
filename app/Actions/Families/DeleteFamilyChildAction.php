<?php

namespace App\Actions\Families;

use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;

class DeleteFamilyChildAction
{
    public function __construct(
        private FamilyChildRepositoryInterface $children,
    ) {}

    public function execute(FamilyChild $child): void
    {
        $this->children->delete($child);
    }
}
