<?php

namespace App\Actions\Families;

use App\Repositories\Contracts\FamilyRepositoryInterface;

class DeleteFamiliesAction
{
    public function __construct(
        private FamilyRepositoryInterface $families,
    ) {}

    /**
     * @param  list<int>  $ids
     */
    public function execute(array $ids): int
    {
        return $this->families->deleteByIds($ids);
    }
}
