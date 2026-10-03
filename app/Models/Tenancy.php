<?php
namespace App\Models;

use App\Enums\TenancyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_application_id', 'renter_id', 'room_id', 'landlord_id',
        'status', 'start_date', 'end_date', 'monthly_rent',
        'completed_at', 'terminated_at', 'termination_reason',
    ];

    protected $casts = [
        'status'         => TenancyStatus::class,
        'start_date'     => 'date',
        'end_date'       => 'date',
        'completed_at'   => 'datetime',
        'terminated_at'  => 'datetime',
        'monthly_rent'   => 'decimal:2',
    ];

    public function application(): BelongsTo { return $this->belongsTo(RentalApplication::class, 'rental_application_id'); }
    public function renter(): BelongsTo       { return $this->belongsTo(User::class, 'renter_id'); }
    public function landlord(): BelongsTo     { return $this->belongsTo(User::class, 'landlord_id'); }
    public function room(): BelongsTo         { return $this->belongsTo(Room::class); }

    public function payments(): HasMany       { return $this->hasMany(Payment::class); }
    public function review(): HasOne          { return $this->hasOne(Review::class); }
    public function trustEvents(): HasMany    { return $this->hasMany(TrustScoreEvent::class); }

    public function isActive(): bool    { return $this->status === TenancyStatus::Active; }
    public function isCompleted(): bool { return $this->status === TenancyStatus::Completed; }
}