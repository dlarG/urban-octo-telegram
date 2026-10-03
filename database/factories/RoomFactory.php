<?php // database/factories/RoomFactory.php
namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\BoardingHouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'boarding_house_id'   => BoardingHouse::factory(),
            'room_label'          => 'Room '.fake()->unique()->numberBetween(1, 500),
            'room_type'           => fake()->randomElement([RoomType::Private, RoomType::Shared]),
            'capacity'            => fake()->numberBetween(1, 4),
            'base_price_monthly'  => fake()->randomFloat(2, 1500, 8000),
            'has_own_bathroom'    => fake()->boolean(70),
            'has_aircon'          => fake()->boolean(30),
            'status'              => RoomStatus::Available,
        ];
    }
}