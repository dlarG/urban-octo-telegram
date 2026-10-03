<?php
namespace Tests\Feature;

use App\Models\{BoardingHouse, PropertyImage, Room, TrustScoreEvent, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_boarding_house_relations(): void
    {
        $bh = BoardingHouse::factory()->has(Room::factory()->count(3))->create();
        $this->assertCount(3, $bh->rooms);
        $this->assertTrue($bh->landlord->isLandlord());
    }

    public function test_property_image_dual_parent_rule(): void
    {
        $this->expectException(\DomainException::class);
        PropertyImage::create([
            'boarding_house_id' => BoardingHouse::factory()->create()->id,
            'room_id'           => Room::factory()->create()->id,
            'path'              => 'x.jpg',
        ]);
    }

    public function test_trust_event_is_append_only(): void
    {
        $event = TrustScoreEvent::factory()->create();
        $this->expectException(\DomainException::class);
        $event->update(['delta' => 999]);
    }
}