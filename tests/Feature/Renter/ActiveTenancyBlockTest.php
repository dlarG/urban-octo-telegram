<?php
namespace Tests\Feature\Renter;

use App\Enums\LandlordApprovalStatus;
use App\Enums\PropertyStatus;
use App\Enums\RoomStatus;
use App\Enums\TenancyStatus;
use App\Models\BoardingHouse;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Models\Tenancy;
use App\Models\TrustScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveTenancyBlockTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRoom(): Room
    {
        $landlord = User::factory()->landlord()->create();
        $landlord->landlordProfile()->create([
            'business_name' => 'X', 'valid_id_path' => 'a', 'business_permit_path' => 'b',
            'documents_submitted_at' => now(),
            'approval_status' => LandlordApprovalStatus::Accepted,
        ]);
        $house = BoardingHouse::factory()->create([
            'landlord_id' => $landlord->id,
            'status'      => PropertyStatus::Active,
        ]);
        return Room::factory()->create([
            'boarding_house_id' => $house->id,
            'status'            => RoomStatus::Available,
        ]);
    }

    public function test_renter_with_active_tenancy_cannot_apply(): void
    {
        $renter = User::factory()->renter()->create();
        TrustScore::create(['user_id' => $renter->id, 'score' => 100]);

        $room = $this->makeRoom();

        // Give them an existing active tenancy on a DIFFERENT room
        $otherRoom = $this->makeRoom();
        $app = RentalApplication::create([
            'renter_id' => $renter->id, 'room_id' => $otherRoom->id, 'status' => 'accepted',
        ]);
        Tenancy::create([
            'rental_application_id' => $app->id,
            'renter_id'             => $renter->id,
            'room_id'               => $otherRoom->id,
            'landlord_id'           => $otherRoom->boardingHouse->landlord_id,
            'status'                => TenancyStatus::Active,
            'start_date'            => now(),
            'monthly_rent'          => 3000,
        ]);

        $this->actingAs($renter)
            ->from(route('properties.show', $room->boardingHouse))
            ->post(route('renter.applications.store', $room), [])
            ->assertSessionHasErrors('general');

        $this->assertEquals(1, RentalApplication::where('renter_id', $renter->id)->count());
    }

    public function test_renter_without_active_tenancy_can_apply(): void
    {
        $renter = User::factory()->renter()->create();
        TrustScore::create(['user_id' => $renter->id, 'score' => 100]);

        $room = $this->makeRoom();

        $this->actingAs($renter)
            ->post(route('renter.applications.store', $room), [])
            ->assertRedirect(route('renter.applications'));

        $this->assertEquals(1, RentalApplication::where('renter_id', $renter->id)->count());
    }
}