<?php

namespace App\Actions\Families;

use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;

class UpdateFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $families,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function execute(Family $family, array $attributes): Family
    {
        return $this->families->update($family, $attributes);
    }
}
