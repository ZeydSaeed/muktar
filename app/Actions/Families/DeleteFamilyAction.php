<?php

namespace App\Actions\Families;

use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;

class DeleteFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $families,
    ) {}

    public function execute(Family $family): void
    {
        $this->families->delete($family);
    }
}
