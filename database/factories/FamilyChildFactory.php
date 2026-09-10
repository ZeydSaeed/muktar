<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\FamilyChild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyChild>
 */
class FamilyChildFactory extends Factory
{
    public function definition(): array
    {
        return [
            'family_id' => Family::factory(),
            'name' => fake()->name(),
            'birth_date' => fake()->optional()->date(),
            'national_id_number' => fake()->optional()->numerify('############'),
            'registry_number' => fake()->optional()->numberBetween(1, 999),
            'page_number' => fake()->optional()->numberBetween(1, 999),
            'profession' => fake()->optional()->jobTitle(),
        ];
    }
}
