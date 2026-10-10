<?php
namespace Database\Seeders;

use App\Enums\LandlordApprovalStatus;
use App\Enums\PropertyStatus;
use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\BoardingHouse;
use App\Models\Room;
use App\Models\User;
use App\Services\TrustScoreService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoLandlordSeeder extends Seeder
{
    public function run(TrustScoreService $trust): void
    {
        DB::transaction(function () use ($trust) {
            $landlord = User::firstOrCreate(
                ['email' => 'demo.landlord@rentstreet.test'],
                [
                    'name'     => 'Demo Landlord',
                    'phone'    => '09170000001',
                    'password' => Hash::make('password'),
                    'role'     => \App\Enums\UserRole::Landlord,
                    'is_active'=> true,
                ]
            );

            if (! $landlord->trustScore) $trust->initializeFor($landlord);

            $landlord->landlordProfile()->updateOrCreate(
                ['user_id' => $landlord->id],
                [
                    'business_name'         => 'Demo Rentals',
                    'approval_status'       => LandlordApprovalStatus::Accepted,
                    'documents_submitted_at'=> now(),
                    'accepted_at'           => now(),
                    'valid_id_path'         => 'demo/valid.pdf',
                    'business_permit_path'  => 'demo/permit.pdf',
                ]
            );

            if ($landlord->boardingHouses()->count() > 0) return;

            $house = BoardingHouse::factory()->create([
                'landlord_id' => $landlord->id,
                'status'      => PropertyStatus::Active,
                'name'        => 'Demo Boarding House',
                'lat'         => 10.3895690,
                'lng'         => 124.9805770,
            ]);

            foreach ([
                ['Room 1', RoomType::Private, 1, 3500],
                ['Room 2', RoomType::Private, 1, 3200],
                ['Room 3', RoomType::Shared,  2, 2000],
            ] as [$label, $type, $capacity, $price]) {
                Room::create([
                    'boarding_house_id'  => $house->id,
                    'room_label'         => $label,
                    'room_type'          => $type,
                    'capacity'           => $capacity,
                    'base_price_monthly' => $price,
                    'has_own_bathroom'   => true,
                    'has_aircon'         => $type === RoomType::Private,
                    'status'             => RoomStatus::Available,
                ]);
            }
        });
    }
}