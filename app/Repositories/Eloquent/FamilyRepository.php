<?php

namespace App\Repositories\Eloquent;

use App\Data\FamilyTableFilters;
use App\Models\Family;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use App\Services\Families\FamilyFilterApplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FamilyRepository implements FamilyRepositoryInterface
{
    public function __construct(
        private FamilyFilterApplier $filterApplier,
    ) {}

    /**
     * @return Collection<int, Family>
     */
    public function all(): Collection
    {
        return Family::query()->latest()->get();
    }

    /**
     * @return LengthAwarePaginator<int, Family>
     */
    public function paginateForDashboard(
        int $perPage,
        ?string $search = null,
        ?FamilyTableFilters $filters = null,
    ): LengthAwarePaginator
    {
        $query = Family::query()
            ->select([
                'id',
                'head_of_family_name',
                'profession',
                'house_number',
                'mobile_number',
                'national_id_number',
                'birth_date',
            ])
            ->withCount('children');

        $this->applyNameSearch($query, $search);

        if ($filters !== null) {
            $this->filterApplier->apply($query, $filters);
        }

        return $query
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  Builder<Family>  $query
     */
    private function applyNameSearch(Builder $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        foreach (preg_split('/\s+/u', $search) ?: [] as $token) {
            if ($token === '') {
                continue;
            }

            $like = '%'.addcslashes($token, '%_\\').'%';
            $query->where('head_of_family_name', 'like', $like);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Family
    {
        return Family::create($attributes);
    }

    public function findById(int $id): Family
    {
        return Family::query()->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Family $family, array $attributes): Family
    {
        $family->update($attributes);

        return $family;
    }

    public function delete(Family $family): void
    {
        $family->delete();
    }

    /**
     * @return Collection<int, Family>
     */
    public function allWithChildren(): Collection
    {
        return Family::query()->with('children')->orderBy('id')->get();
    }

    public function count(): int
    {
        return Family::query()->count();
    }

    public function deleteAll(): void
    {
        Family::query()->delete();
    }
}
