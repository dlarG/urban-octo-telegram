<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'boarding_house_id', 'room_id', 'path',
        'is_primary', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    // ---- Explicit FK relationships (v1 gotcha #4) ----
    public function boardingHouse(): BelongsTo
    {
        return $this->belongsTo(BoardingHouse::class, 'boarding_house_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // ---- Scopes ----
    public function scopeForHouse(Builder $q): Builder { return $q->whereNotNull('boarding_house_id'); }
    public function scopeForRoom(Builder $q): Builder  { return $q->whereNotNull('room_id'); }
    public function scopePrimary(Builder $q): Builder  { return $q->where('is_primary', true); }

    // ---- Parent helper (returns whichever parent is set) ----
    public function parent(): BoardingHouse|Room|null
    {
        return $this->boardingHouse ?? $this->room;
    }

    protected static function booted(): void
    {
        // Rule: exactly one of boarding_house_id / room_id must be set
        static::saving(function (self $image) {
            $hasHouse = !is_null($image->boarding_house_id);
            $hasRoom  = !is_null($image->room_id);
            if ($hasHouse === $hasRoom) {
                throw new \DomainException('PropertyImage must belong to exactly one of: boarding house OR room.');
            }
        });

        // Rule: first upload auto-becomes primary; deleting primary auto-promotes next
        static::created(function (self $image) {
            $siblings = static::query()
                ->when($image->boarding_house_id, fn($q) => $q->where('boarding_house_id', $image->boarding_house_id))
                ->when($image->room_id,          fn($q) => $q->where('room_id', $image->room_id))
                ->where('id', '!=', $image->id);

            if (! $siblings->clone()->where('is_primary', true)->exists()) {
                $image->forceFill(['is_primary' => true])->save();
            }
        });

        static::deleted(function (self $image) {
            if (! $image->is_primary) return;

            $next = static::query()
                ->when($image->boarding_house_id, fn($q) => $q->where('boarding_house_id', $image->boarding_house_id))
                ->when($image->room_id,          fn($q) => $q->where('room_id', $image->room_id))
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            $next?->forceFill(['is_primary' => true])->save();
        });
    }
}