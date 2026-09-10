<?php

namespace App\Repositories\Eloquent;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FamilyChildRepository implements FamilyChildRepositoryInterface
{
    /**
     * @return Collection<int, FamilyChild>
     */
    public function listForFamily(Family $family): Collection
    {
        return $family->children()->get();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Family $family, array $attributes): FamilyChild
    {
        return $family->children()->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(FamilyChild $child, array $attributes): FamilyChild
    {
        $child->update($attributes);

        return $child;
    }

    public function delete(FamilyChild $child): void
    {
        $child->delete();
    }

    public function count(): int
    {
        return FamilyChild::query()->count();
    }

    public function deleteAll(): void
    {
        FamilyChild::query()->delete();
    }
}
