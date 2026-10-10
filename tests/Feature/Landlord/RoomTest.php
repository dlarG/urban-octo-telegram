<?php
namespace Tests\Feature\Landlord;

use App\Enums\LandlordApprovalStatus;
use App\Models\BoardingHouse;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    protected function approvedLandlord(): User
    {
        $u = User::factory()->landlord()->create();
        $u->landlordProfile()->create([
            'business_name'          => 'Test Co.',
            'valid_id_path'          => 'x.pdf',
            'business_permit_path'   => 'y.pdf',
            'documents_submitted_at' => now(),
            'approval_status'        => LandlordApprovalStatus::Accepted,
        ]);
        return $u;
    }

    public function test_landlord_can_create_room_on_own_property(): void
    {
        $landlord = $this->approvedLandlord();
        $house = BoardingHouse::factory()->create(['landlord_id' => $landlord->id]);

        $response = $this->actingAs($landlord)->post(
            route('landlord.properties.rooms.store', $house),
            [
                'room_label'         => 'Room 1',
                'room_type'          => 'private',
                'capacity'           => 1,
                'base_price_monthly' => 3500,
                'status'             => 'available',
            ]
        );

        $room = Room::first();
        $this->assertNotNull($room);
        $this->assertEquals('Room 1', $room->room_label);
        $response->assertRedirect(route('landlord.properties.rooms.show', [$house, $room]));
    }

    public function test_landlord_cannot_create_room_on_someone_elses_property(): void
    {
        $landlord = $this->approvedLandlord();
        $other    = User::factory()->landlord()->create();
        $house    = BoardingHouse::factory()->create(['landlord_id' => $other->id]);

        $this->actingAs($landlord)
            ->get(route('landlord.properties.rooms.create', $house))
            ->assertForbidden();
    }
}