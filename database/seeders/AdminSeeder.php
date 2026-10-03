<?php
namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\TrustScoreService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(TrustScoreService $trust): void
    {
        DB::transaction(function () use ($trust) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@rentstreet.test'],
                [
                    'name'     => 'Platform Admin',
                    'phone'    => '09000000000',
                    'password' => Hash::make('password'),
                    'role'     => UserRole::Admin,
                    'is_active'=> true,
                ]
            );

            // Invariant: every user gets a trust score row.
            if (! $admin->trustScore) {
                $trust->initializeFor($admin);
            }
        });
    }
}