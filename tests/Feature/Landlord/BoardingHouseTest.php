<?php
namespace Tests\Feature\Landlord;

use App\Enums\LandlordApprovalStatus;
use App\Enums\PropertyStatus;
use App\Models\BoardingHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardingHouseTest extends TestCase
{
    use RefreshDatabase;

    protected function approvedLandlord(): User
    {
        $u = User::factory()->landlord()->create();
        $u->landlordProfile()->create([
            'approval_status'         => LandlordApprovalStatus::Accepted,
            'onboarding_completed_at' => now(),
            'business_name'           => 'Test Co.',
            'valid_id_path'           => 'x.jpg',
            'business_permit_path'    => 'y.jpg',
        ]);
        return $u;
    }

    public function test_landlord_with_pending_approval_cannot_create_property(): void
    {
        $u = User::factory()->landlord()->create();
        $u->landlordProfile()->create([
            'approval_status'         => LandlordApprovalStatus::Pending,
            'onboarding_completed_at' => now(),
            'business_name'           => 'Pending Co.',
            'valid_id_path'           => 'x.jpg',
            'business_permit_path'    => 'y.jpg',
        ]);

        $this->actingAs($u)
            ->get(route('landlord.properties.create'))
            ->assertForbidden();
    }

    public function test_approved_landlord_can_create_property(): void
    {
        $landlord = $this->approvedLandlord();

        $response = $this->actingAs($landlord)->post(route('landlord.properties.store'), [
            'name'                => 'Sunrise Boarding House',
            'address_line'        => '123 Rizal St.',
            'barangay'            => 'Poblacion',
            'city'                => 'Sogod',
            'province'            => 'Southern Leyte',
            'lat'                 => 10.3895690,
            'lng'                 => 124.9805770,
            'gender_policy'       => 'mixed',
            'allows_cooking'      => 1,
            'is_sub_metered'      => 0,
        ]);

        $house = BoardingHouse::first();
        $this->assertNotNull($house);
        $this->assertEquals(PropertyStatus::PendingReview, $house->status);
        $response->assertRedirect(route('landlord.properties.show', $house));
    }

    public function test_editing_inactive_property_flips_to_pending_review(): void
    {
        $landlord = $this->approvedLandlord();
        $house = BoardingHouse::factory()->create([
            'landlord_id' => $landlord->id,
            'status'      => PropertyStatus::Inactive,
        ]);

        $this->actingAs($landlord)->put(route('landlord.properties.update', $house), [
            'name'                => $house->name,
            'address_line'        => 'Updated',
            'barangay'            => $house->barangay,
            'city'                => $house->city,
            'province'            => $house->province,
            'lat'                 => $house->lat,
            'lng'                 => $house->lng,
            'gender_policy'       => $house->gender_policy,
        ])->assertRedirect();

        $this->assertEquals(PropertyStatus::PendingReview, $house->fresh()->status);
    }

    public function test_suspended_property_cannot_be_edited(): void
    {
        $landlord = $this->approvedLandlord();
        $house = BoardingHouse::factory()->create([
            'landlord_id' => $landlord->id,
            'status'      => PropertyStatus::Suspended,
        ]);

        $this->actingAs($landlord)
            ->get(route('landlord.properties.edit', $house))
            ->assertForbidden();
    }
}