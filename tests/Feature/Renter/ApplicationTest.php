<?php
namespace Tests\Feature\Renter;

use App\Enums\ApplicationStatus;
use App\Enums\PropertyStatus;
use App\Enums\RoomStatus;
use App\Models\BoardingHouse;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRoom(): Room
    {
        $landlord = User::factory()->landlord()->create();
        $house    = BoardingHouse::factory()->create([
            'landlord_id' => $landlord->id,
            'status'      => PropertyStatus::Active,
        ]);
        return Room::factory()->create([
            'boarding_house_id' => $house->id,
            'status'            => RoomStatus::Available,
        ]);
    }

    public function test_renter_can_apply_to_available_room(): void
    {
        $room   = $this->makeRoom();
        $renter = User::factory()->renter()->create();

        $this->actingAs($renter)->post(route('renter.applications.store', $room), [
            'message' => 'I am a quiet student.',
        ])->assertRedirect(route('renter.applications'));

        $this->assertDatabaseHas('rental_applications', [
            'renter_id' => $renter->id,
            'room_id'   => $room->id,
            'status'    => ApplicationStatus::Submitted->value,
        ]);
    }

    public function test_per_room_duplicate_is_blocked(): void
    {
        $room   = $this->makeRoom();
        $renter = User::factory()->renter()->create();

        RentalApplication::create([
            'renter_id' => $renter->id,
            'room_id'   => $room->id,
            'status'    => ApplicationStatus::Submitted,
        ]);

        $this->actingAs($renter)
            ->from(route('properties.show', $room->boardingHouse))
            ->post(route('renter.applications.store', $room), [])
            ->assertSessionHasErrors('general');

        $this->assertEquals(
            1,
            RentalApplication::where('renter_id', $renter->id)->where('room_id', $room->id)->count()
        );
    }

    public function test_renter_can_apply_to_same_property_different_room(): void
    {
        $room1 = $this->makeRoom();
        $room2 = Room::factory()->create([
            'boarding_house_id' => $room1->boarding_house_id,
            'status'            => RoomStatus::Available,
        ]);
        $renter = User::factory()->renter()->create();

        $this->actingAs($renter)->post(route('renter.applications.store', $room1), []);
        $this->actingAs($renter)->post(route('renter.applications.store', $room2), []);

        $this->assertEquals(2, RentalApplication::where('renter_id', $renter->id)->count());
    }

    public function test_withdrawn_application_does_not_block(): void
    {
        $room   = $this->makeRoom();
        $renter = User::factory()->renter()->create();

        $app = RentalApplication::create([
            'renter_id' => $renter->id,
            'room_id'   => $room->id,
            'status'    => ApplicationStatus::Withdrawn,
        ]);

        $this->actingAs($renter)
            ->post(route('renter.applications.store', $room), [])
            ->assertRedirect(route('renter.applications'));

        $this->assertEquals(2, RentalApplication::where('renter_id', $renter->id)->count());
    }

    public function test_renter_cannot_apply_to_full_room(): void
    {
        $room = $this->makeRoom();
        $room->update(['status' => RoomStatus::Full]);

        $renter = User::factory()->renter()->create();

        $this->actingAs($renter)
            ->from(route('properties.show', $room->boardingHouse))
            ->post(route('renter.applications.store', $room), [])
            ->assertSessionHasErrors('general');
    }
}