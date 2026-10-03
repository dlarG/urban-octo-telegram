<?php
namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'renter_id', 'room_id', 'status', 'message',
        'response_message', 'viewed_at', 'responded_at',
    ];

    protected $casts = [
        'status'       => ApplicationStatus::class,
        'viewed_at'    => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function renter(): BelongsTo { return $this->belongsTo(User::class, 'renter_id'); }
    public function room(): BelongsTo   { return $this->belongsTo(Room::class); }
    public function tenancy(): HasOne   { return $this->hasOne(Tenancy::class); }

    // Convenience: landlord via room → boarding house
    public function landlord(): ?User
    {
        return $this->room?->boardingHouse?->landlord;
    }

    public function scopeBlocking(Builder $q): Builder
    {
        return $q->whereIn('status', [
            ApplicationStatus::Submitted,
            ApplicationStatus::Viewed,
            ApplicationStatus::Accepted,
        ]);
    }
}