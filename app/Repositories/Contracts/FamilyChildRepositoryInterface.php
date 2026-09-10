<?php

namespace App\Repositories\Contracts;

use App\Models\Family;
use App\Models\FamilyChild;
use Illuminate\Database\Eloquent\Collection;

interface FamilyChildRepositoryInterface
{
    /**
     * @return Collection<int, FamilyChild>
     */
    public function listForFamily(Family $family): Collection;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Family $family, array $attributes): FamilyChild;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(FamilyChild $child, array $attributes): FamilyChild;

    public function delete(FamilyChild $child): void;

    public function count(): int;

    public function deleteAll(): void;
}
