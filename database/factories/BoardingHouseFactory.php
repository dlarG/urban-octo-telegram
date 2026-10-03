<?php // database/factories/BoardingHouseFactory.php
namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardingHouseFactory extends Factory
{
    public function definition(): array
    {
        // ~0.02 degrees ≈ 2.2km at this latitude
        $jitter = fn() => fake()->randomFloat(7, -0.02, 0.02);

        return [
            'landlord_id'         => User::factory()->landlord(),
            'name'                => fake()->company().' Boarding House',
            'description'         => fake()->paragraph(),
            'address_line'        => fake()->streetAddress(),
            'barangay'            => fake()->randomElement(['Poblacion', 'Zone I', 'Zone II', 'San Juan', 'Benit']),
            'city'                => 'Sogod',
            'province'            => 'Southern Leyte',
            'lat'                 => 10.389569 + $jitter(),
            'lng'                 => 124.980577 + $jitter(),
            'curfew_time'         => fake()->randomElement(['21:00', '22:00', '23:00', null]),
            'allows_cooking'      => fake()->boolean(60),
            'gender_policy'       => fake()->randomElement(['male_only', 'female_only', 'mixed']),
            'water_supply_rating' => fake()->numberBetween(1, 5),
            'is_sub_metered'      => fake()->boolean(50),
            'status'              => PropertyStatus::Active,
        ];
    }
}