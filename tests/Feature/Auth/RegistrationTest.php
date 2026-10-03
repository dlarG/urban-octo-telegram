<?php
namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_renter_registration_creates_trust_score_row_in_one_transaction(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/register', [
            'name'        => 'Test Renter',
            'email'       => 'renter@example.test',
            'phone'       => '09171234567',
            'password'    => 'password',
            'password_confirmation' => 'password',
            'role'        => 'renter',
            'renter_type' => 'student',
            'terms'       => '1',
        ]);

        $response->assertRedirect(route('renter.dashboard'));

        $user = User::where('email', 'renter@example.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Renter, $user->role);
        $this->assertNotNull($user->renterProfile);

        // 🔒 The invariant:
        $this->assertNotNull($user->trustScore);
        $this->assertEquals('100.00', $user->trustScore->score);
        $this->assertNull($user->trustScore->last_event_at);
    }

    public function test_landlord_registration_also_creates_trust_score_row(): void
    {
        $this->post('/register', [
            'name' => 'Test Landlord',
            'email' => 'landlord@example.test',
            'phone' => '09181234567',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'landlord',
            'terms' => '1',
        ])->assertRedirect(route('landlord.onboarding'));

        $user = User::where('email', 'landlord@example.test')->first();
        $this->assertNotNull($user->trustScore);
    }

    public function test_registration_rolls_back_if_trust_score_creation_fails(): void
    {
        // Force failure by mocking the service — proves atomicity
        $mock = $this->mock(\App\Services\TrustScoreService::class);
        $mock->shouldReceive('initializeFor')->once()->andThrow(new \RuntimeException('boom'));

        try {
            $this->post('/register', [
                'name' => 'Rollback Test',
                'email' => 'rollback@example.test',
                'phone' => '09191234567',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'renter',
                'renter_type' => 'student',
                'terms' => '1',
            ]);
        } catch (\RuntimeException $e) {
            // expected
        }

        $this->assertDatabaseMissing('users', ['email' => 'rollback@example.test']);
    }
}