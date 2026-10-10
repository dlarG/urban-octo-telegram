<?php
namespace Tests\Feature\Landlord;

use App\Enums\LandlordApprovalStatus;
use App\Enums\PropertyStatus;
use App\Models\BoardingHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BoardingHouseTest extends TestCase
{
    use RefreshDatabase;

    protected function approvedLandlord(): User
    {
        $u = User::factory()->landlord()->create();
        $u->landlordProfile()->create([
            'approval_status'         => LandlordApprovalStatus::Accepted,
            'documents_submitted_at' => now(),
            'business_name'           => 'Test Co.',
            'valid_id_path'           => 'x.jpg',
            'business_permit_path'    => 'y.jpg',
        ]);
        return $u;
    }

    public function test_landlord_registration_stores_documents(): void
    {
        Storage::fake('local');

        $this->post('/register', [
            'name'                 => 'Doc Landlord',
            'email'                => 'doc@example.test',
            'phone'                => '09171234599',
            'password'             => 'password',
            'password_confirmation' => 'password',
            'role'                 => 'landlord',
            'business_name'        => 'Doc Test Co.',
            'gcash_number'         => '09171234599',
            'valid_id'             => UploadedFile::fake()->image('id.jpg'),
            'business_permit'      => UploadedFile::fake()->image('permit.jpg'),
            'terms'                => '1',
        ])->assertRedirect(route('landlord.dashboard'));

        $profile = User::where('email', 'doc@example.test')->first()->landlordProfile;

        $this->assertNotNull($profile->valid_id_path);
        $this->assertNotNull($profile->business_permit_path);
        $this->assertNotNull($profile->documents_submitted_at);
        Storage::disk('local')->assertExists($profile->valid_id_path);
        Storage::disk('local')->assertExists($profile->business_permit_path);
    }

    public function test_landlord_with_pending_approval_cannot_create_property(): void
    {
        $u = User::factory()->landlord()->create();
        $u->landlordProfile()->create([
            'approval_status'         => LandlordApprovalStatus::Pending,
            'documents_submitted_at' => now(),
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