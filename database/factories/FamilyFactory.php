<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'head_of_family_name' => fake()->name(),
            'profession' => fake()->optional()->jobTitle(),
            'house_number' => fake()->optional()->numberBetween(1, 999),
            'mobile_number' => fake()->optional()->numerify('07#########'),
            'national_id_number' => fake()->optional()->numerify('############'),
            'birth_date' => fake()->optional()->date(),
        ];
    }
}
