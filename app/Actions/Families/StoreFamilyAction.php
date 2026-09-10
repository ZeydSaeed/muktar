<?php

namespace App\Actions\Families;

use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;

class StoreFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $families,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function execute(array $attributes): Family
    {
        return $this->families->create($attributes);
    }
}
