<?php

namespace App\Models;

use Database\Factories\FamilyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $head_of_family_name
 * @property string|null $spouse_name
 * @property string|null $profession
 * @property string|null $mother_name
 * @property int|null $house_number
 * @property int|null $ration_card_number
 * @property int|null $housing_card_number
 * @property string|null $national_id_number
 * @property string|null $mobile_number
 * @property Carbon|null $current_residence_date
 * @property Carbon|null $birth_date
 * @property int|null $family_members_count
 * @property int|null $males_count
 * @property int|null $females_count
 * @property string|null $previous_address
 * @property string|null $previous_mukhtar_name
 * @property Carbon|null $karbala_residence_date
 * @property int|null $registry_page_number
 * @property string|null $nearest_landmark
 * @property string|null $secondary_mobile_number
 * @property string|null $birth_place
 * @property string|null $ration_agent_name
 * @property Carbon|null $housing_card_date
 * @property string|null $issuing_authority
 * @property string|null $current_governorate
 * @property string|null $current_district
 * @property string|null $current_subdistrict
 * @property string|null $current_neighborhood
 * @property string|null $current_alley
 * @property string|null $previous_governorate
 * @property string|null $previous_district
 * @property string|null $previous_subdistrict
 * @property string|null $previous_neighborhood
 * @property string|null $previous_landmark
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FamilyChild> $children
 */
#[Fillable([
    'head_of_family_name',
    'spouse_name',
    'profession',
    'mother_name',
    'house_number',
    'ration_card_number',
    'housing_card_number',
    'national_id_number',
    'mobile_number',
    'current_residence_date',
    'birth_date',
    'birth_place',
    'family_members_count',
    'males_count',
    'females_count',
    'ration_agent_name',
    'housing_card_date',
    'issuing_authority',
    'current_governorate',
    'current_district',
    'current_subdistrict',
    'current_neighborhood',
    'current_alley',
    'previous_address',
    'previous_governorate',
    'previous_district',
    'previous_subdistrict',
    'previous_neighborhood',
    'previous_landmark',
    'previous_mukhtar_name',
    'karbala_residence_date',
    'registry_page_number',
    'nearest_landmark',
    'secondary_mobile_number',
    'notes',
])]
class Family extends Model
{
    /** @use HasFactory<FamilyFactory> */
    use HasFactory;

    /**
     * @return HasMany<FamilyChild, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(FamilyChild::class)->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_residence_date' => 'date',
            'birth_date' => 'date',
            'karbala_residence_date' => 'date',
            'housing_card_date' => 'date',
        ];
    }
}
