<?php

namespace App\Models;

use Database\Factories\FamilyChildFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $family_id
 * @property string $name
 * @property Carbon|null $birth_date
 * @property string|null $national_id_number
 * @property int|null $registry_number
 * @property int|null $page_number
 * @property string|null $profession
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Family $family
 */
#[Fillable([
    'family_id',
    'name',
    'birth_date',
    'national_id_number',
    'registry_number',
    'page_number',
    'profession',
])]
class FamilyChild extends Model
{
    /** @use HasFactory<FamilyChildFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Family, $this>
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
