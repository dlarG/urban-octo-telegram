<?php
namespace App\Services;

use App\Models\BoardingHouse;
use App\Models\PropertyImage;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PropertyImageService
{
    public function __construct(protected StorageService $storage) {}

    /**
     * Attach an uploaded image to either a boarding house or a room.
     */
    public function attach(BoardingHouse|Room $parent, UploadedFile $file): PropertyImage
    {
        return DB::transaction(function () use ($parent, $file) {
            $category = $parent instanceof BoardingHouse
                ? 'properties/houses/'.$parent->id
                : 'properties/rooms/'.$parent->id;

            $path = $this->storage->put($file, $category);

            return PropertyImage::create([
                'boarding_house_id' => $parent instanceof BoardingHouse ? $parent->id : null,
                'room_id'           => $parent instanceof Room ? $parent->id : null,
                'path'              => $path,
                'sort_order'        => $this->nextSortOrder($parent),
            ]);
        });
    }

    /**
     * Delete an image. Model hook auto-promotes the next primary.
     */
    public function delete(PropertyImage $image): void
    {
        $this->storage->delete($image->path);
        $image->delete();
    }

    /**
     * Manually mark an image as the cover. Unsets any previous primary.
     */
    public function makePrimary(PropertyImage $image): void
    {
        DB::transaction(function () use ($image) {
            $query = PropertyImage::query()
                ->when($image->boarding_house_id, fn($q) => $q->where('boarding_house_id', $image->boarding_house_id))
                ->when($image->room_id,          fn($q) => $q->where('room_id', $image->room_id));

            $query->update(['is_primary' => false]);

            $image->forceFill(['is_primary' => true])->save();
        });
    }

    protected function nextSortOrder(BoardingHouse|Room $parent): int
    {
        return PropertyImage::query()
            ->when($parent instanceof BoardingHouse, fn($q) => $q->where('boarding_house_id', $parent->id))
            ->when($parent instanceof Room,          fn($q) => $q->where('room_id', $parent->id))
            ->max('sort_order') + 1;
    }
}