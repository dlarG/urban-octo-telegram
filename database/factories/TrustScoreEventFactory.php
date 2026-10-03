<?php

namespace Database\Factories;

use App\Enums\TrustEventType;
use App\Models\TrustScoreEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrustScoreEvent>
 */
class TrustScoreEventFactory extends Factory
{
    protected $model = TrustScoreEvent::class;

    public function definition(): array
    {
        $type  = fake()->randomElement(TrustEventType::cases());
        $delta = $type->defaultDelta();

        return [
            'user_id'    => User::factory()->renter(),
            'tenancy_id' => null,
            'payment_id' => null,
            'dispute_id' => null,
            'event_type' => $type,
            'delta'      => $delta,
            'score_after'=> 100.00 + $delta,
            'reason'     => fake()->optional()->sentence(),
            'created_by' => null,
        ];
    }
}