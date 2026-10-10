<?php
namespace Tests\Feature\Landlord;

use App\Enums\ApplicationStatus;
use App\Enums\LandlordApprovalStatus;
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

class TenancyFlowTest extends TestCase
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

    protected function makeApplication(): RentalApplication
    {
        $landlord = $this->approvedLandlord();
        $house    = BoardingHouse::factory()->create(['landlord_id' => $landlord->id]);
        $room     = Room::factory()->create([
            'boarding_house_id' => $house->id,
            'capacity'          => 1,
            'status'            => RoomStatus::Available,
        ]);
        $renter = User::factory()->renter()->create();
        TrustScore::create(['user_id' => $renter->id, 'score' => 100]);

        return RentalApplication::create([
            'renter_id' => $renter->id,
            'room_id'   => $room->id,
            'status'    => ApplicationStatus::Submitted,
        ]);
    }

    public function test_landlord_accepts_application_creates_tenancy(): void
    {
        $app = $this->makeApplication();
        $landlord = $app->room->boardingHouse->landlord;

        $this->actingAs($landlord)
            ->post(route('landlord.applications.accept', $app), [
                'start_date'   => now()->addWeek()->toDateString(),
                'monthly_rent' => 3500,
            ])
            ->assertRedirect(route('landlord.applications.show', $app));

        $this->assertDatabaseHas('tenancies', [
            'rental_application_id' => $app->id,
            'status'                => TenancyStatus::Active->value,
        ]);

        $this->assertEquals(ApplicationStatus::Accepted, $app->fresh()->status);
    }

    public function test_private_room_flips_to_full_on_acceptance(): void
    {
        $app = $this->makeApplication();
        $landlord = $app->room->boardingHouse->landlord;

        $this->actingAs($landlord)
            ->post(route('landlord.applications.accept', $app), []);

        $this->assertEquals(RoomStatus::Full, $app->room->fresh()->status);
    }

    public function test_competing_applications_are_auto_rejected(): void
    {
        $app = $this->makeApplication();
        $room = $app->room;

        $otherRenter = User::factory()->renter()->create();
        TrustScore::create(['user_id' => $otherRenter->id, 'score' => 100]);
        $other = RentalApplication::create([
            'renter_id' => $otherRenter->id,
            'room_id'   => $room->id,
            'status'    => ApplicationStatus::Submitted,
        ]);

        $landlord = $room->boardingHouse->landlord;
        $this->actingAs($landlord)
            ->post(route('landlord.applications.accept', $app), []);

        $this->assertEquals(ApplicationStatus::Rejected, $other->fresh()->status);
    }

    public function test_landlord_trust_score_view_is_logged(): void
    {
        $this->withoutExceptionHandling();
        $app = $this->makeApplication();
        $landlord = $app->room->boardingHouse->landlord;

        $response = $this->actingAs($landlord)
            ->get(route('landlord.applications.show', $app));

        $response->assertOk();     // will fail loudly if the page errors

        $this->assertDatabaseHas('trust_score_access_log', [
            'viewer_id'             => $landlord->id,
            'subject_id'            => $app->renter_id,
            'rental_application_id' => $app->id,
        ]);
    }

    public function test_landlord_cannot_act_on_someone_elses_application(): void
    {
        $app     = $this->makeApplication();
        $other   = $this->approvedLandlord();

        $this->actingAs($other)
            ->post(route('landlord.applications.accept', $app), [])
            ->assertForbidden();
    }

    public function test_ending_tenancy_frees_private_room(): void
    {
        $app = $this->makeApplication();
        $landlord = $app->room->boardingHouse->landlord;

        $this->actingAs($landlord)->post(route('landlord.applications.accept', $app), []);
        $tenancy = Tenancy::first();

        $this->actingAs($landlord)
            ->post(route('landlord.tenancies.end', $tenancy), ['reason' => 'Renter moved out']);

        $this->assertEquals(TenancyStatus::Completed, $tenancy->fresh()->status);
        $this->assertEquals(RoomStatus::Available, $app->room->fresh()->status);
    }
}