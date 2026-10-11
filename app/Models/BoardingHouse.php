<?php
namespace App\Models;

use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BoardingHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id', 'name', 'description',
        'address_line', 'barangay', 'city', 'province',
        'lat', 'lng',
        'curfew_time', 'allows_cooking', 'gender_policy',
        'water_supply_rating', 'is_sub_metered',
        'status', 'rejection_reason', 'suspension_reason',
        'status_updated_by', 'status_updated_at',
    ];

    protected $casts = [
        'status'               => PropertyStatus::class,
        'allows_cooking'       => 'boolean',
        'is_sub_metered'       => 'boolean',
        'lat'                  => 'decimal:7',
        'lng'                  => 'decimal:7',
        'status_updated_at'    => 'datetime',
    ];

    // ---- Relations ----
    public function landlord(): BelongsTo    { return $this->belongsTo(User::class, 'landlord_id'); }
    public function statusUpdater(): BelongsTo { return $this->belongsTo(User::class, 'status_updated_by'); }

    public function rooms(): HasMany         { return $this->hasMany(Room::class); }
    public function applications(): HasManyThrough
    {
        // applications belong to rooms, rooms belong to boarding houses
        return $this->hasManyThrough(RentalApplication::class, Room::class);
    }
    public function tenancies(): HasManyThrough
    {
        return $this->hasManyThrough(Tenancy::class, Room::class);
    }
    public function images(): MorphMany
    {
        // Not truly polymorphic in DB (two FK columns), but we model as morphMany for API symmetry
        return $this->morphMany(PropertyImage::class, 'imageable');
    }
    public function propertyImages(): HasMany
    {
        // The *real* relationship given our schema (boarding_house_id FK)
        return $this->hasMany(PropertyImage::class);
    }
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'boarding_house_amenities')->withTimestamps();
    }
    public function getPriceRangeAttribute(): ?array
    {
        $min = $this->rooms_min_base_price_monthly ?? null;
        $max = $this->rooms_max_base_price_monthly ?? null;

        if ($min === null && $max === null) return null;
        return ['min' => (float) $min, 'max' => (float) $max];
    }

    public function getPriceRangeLabelAttribute(): ?string
    {
        $range = $this->price_range;
        if (! $range) return null;

        $min = number_format($range['min'], 0);
        $max = number_format($range['max'], 0);

        return $min === $max ? "₱{$min}" : "₱{$min} – ₱{$max}";
    }
    public function favorites(): HasMany     { return $this->hasMany(Favorite::class); }
    public function reviews(): HasMany       { return $this->hasMany(Review::class); }

    // ---- Scopes ----
    public function scopeActive(Builder $q): Builder        { return $q->where('status', PropertyStatus::Active); }
    public function scopePendingReview(Builder $q): Builder { return $q->where('status', PropertyStatus::PendingReview); }
    public function scopeSuspended(Builder $q): Builder     { return $q->where('status', PropertyStatus::Suspended); }

    public function scopeNear(Builder $q, float $lat, float $lng, float $radiusKm = 10): Builder
    {
        // Haversine in SQL — works on plain DECIMAL lat/lng, no spatial index needed
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat))))';
        return $q->selectRaw("boarding_houses.*, {$haversine} AS distance_km", [$lat, $lng, $lat])
                 ->having('distance_km', '<=', $radiusKm)
                 ->orderBy('distance_km');
    }

    // ---- Lifecycle rules ----
    /**
     * Rule: any edit to an inactive property auto-flips it to pending_review.
     * Suspended is a separate, admin-only-to-lift state — edits blocked entirely.
     */
    public function guardEditAllowed(): void
    {
        if ($this->status === PropertyStatus::Suspended) {
            throw new \DomainException('Suspended properties cannot be edited.');
        }
    }

    public function afterLandlordEdit(): void
    {
        if ($this->status === PropertyStatus::Inactive
            || $this->status === PropertyStatus::PendingReview
            || $this->status === PropertyStatus::Active
        ) {
            // Any edit that changes substantive fields should re-enter review
            $this->forceFill([
                'status'             => PropertyStatus::PendingReview,
                'status_updated_at'  => now(),
            ])->save();
        }
    }

    public function primaryImage(): ?PropertyImage
    {
        return $this->propertyImages()->where('is_primary', true)->first()
            ?? $this->propertyImages()->orderBy('sort_order')->first();
    }
}