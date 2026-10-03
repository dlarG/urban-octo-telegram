<?php // database/factories/UserFactory.php
namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'phone'              => '09'.fake()->unique()->numerify('#########'),
            'email_verified_at'  => now(),
            'password'           => Hash::make('password'),
            'role'               => UserRole::Renter,
            'is_active'          => true,
            'remember_token'     => Str::random(10),
        ];
    }

    public function admin(): static    { return $this->state(['role' => UserRole::Admin]); }
    public function landlord(): static { return $this->state(['role' => UserRole::Landlord]); }
    public function renter(): static   { return $this->state(['role' => UserRole::Renter]); }
}