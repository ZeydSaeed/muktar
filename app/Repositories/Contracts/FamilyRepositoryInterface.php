<?php

namespace App\Repositories\Contracts;

use App\Data\FamilyTableFilters;
use App\Models\Family;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FamilyRepositoryInterface
{
    /**
     * @return Collection<int, Family>
     */
    public function all(): Collection;

    /**
     * @return LengthAwarePaginator<int, Family>
     */
    public function paginateForDashboard(
        int $perPage,
        ?string $search = null,
        ?FamilyTableFilters $filters = null,
    ): LengthAwarePaginator;

    public function create(array $attributes): Family;

    public function findById(int $id): Family;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Family $family, array $attributes): Family;

    public function delete(Family $family): void;

    /**
     * @return Collection<int, Family>
     */
    public function allWithChildren(): Collection;

    public function count(): int;

    public function deleteAll(): void;
}
