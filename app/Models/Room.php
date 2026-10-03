<?php
namespace App\Models;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'boarding_house_id', 'room_label', 'room_type', 'capacity',
        'base_price_monthly', 'has_own_bathroom', 'has_aircon', 'status',
    ];

    protected $casts = [
        'room_type'           => RoomType::class,
        'status'              => RoomStatus::class,
        'has_own_bathroom'    => 'boolean',
        'has_aircon'          => 'boolean',
        'base_price_monthly'  => 'decimal:2',
    ];

    public function boardingHouse(): BelongsTo { return $this->belongsTo(BoardingHouse::class); }
    public function bedSlots(): HasMany        { return $this->hasMany(BedSlot::class); }
    public function applications(): HasMany    { return $this->hasMany(RentalApplication::class); }
    public function tenancies(): HasMany       { return $this->hasMany(Tenancy::class); }
    public function propertyImages(): HasMany  { return $this->hasMany(PropertyImage::class); }

    public function scopeAvailable(Builder $q): Builder { return $q->where('status', RoomStatus::Available); }

    public function primaryImage(): ?PropertyImage
    {
        return $this->propertyImages()->where('is_primary', true)->first()
            ?? $this->propertyImages()->orderBy('sort_order')->first();
    }

    /**
     * Per-room duplicate prevention.
     * Return true if $renterId already has a blocking application on this room.
     */
    public function hasBlockingApplicationFrom(int $renterId): bool
    {
        return $this->applications()
            ->where('renter_id', $renterId)
            ->whereIn('status', [
                \App\Enums\ApplicationStatus::Submitted,
                \App\Enums\ApplicationStatus::Viewed,
                \App\Enums\ApplicationStatus::Accepted,
            ])
            ->exists();
    }
}