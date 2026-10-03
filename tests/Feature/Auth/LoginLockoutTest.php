<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_locks_after_three_failed_attempts(): void
    {
        $user = User::factory()->renter()->create(['email' => 'lock@example.test']);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'email' => 'lock@example.test',
                'password' => 'wrong-password',
            ]);
        }

        $user->refresh();
        $this->assertEquals(3, $user->failed_login_attempts);
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->isLockedOut());
    }

    public function test_successful_login_resets_counters(): void
    {
        $user = User::factory()->renter()->create([
            'email' => 'reset@example.test',
            'password' => bcrypt('correct-password'),
            'failed_login_attempts' => 2,
        ]);

        $this->post('/login', [
            'email' => 'reset@example.test',
            'password' => 'correct-password',
        ])->assertRedirect(route('renter.dashboard'));

        $user->refresh();
        $this->assertEquals(0, $user->failed_login_attempts);
        $this->assertNull($user->locked_until);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->renter()->create([
            'email' => 'inactive@example.test',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => 'inactive@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }
}