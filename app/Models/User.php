<?php
namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\Deactivatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Deactivatable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'profile_photo_path', 'is_active',
        'registration_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'password'               => 'hashed',
        'role'                   => UserRole::class,
        'is_active'              => 'boolean',
        'locked_until'           => 'datetime',
        'last_login_at'          => 'datetime',
    ];

    // ---- Role helpers ----
    public function isAdmin(): bool    { return $this->role === UserRole::Admin; }
    public function isLandlord(): bool { return $this->role === UserRole::Landlord; }
    public function isRenter(): bool   { return $this->role === UserRole::Renter; }

    // ---- Profile relations ----
    public function renterProfile(): HasOne    { return $this->hasOne(RenterProfile::class); }
    public function landlordProfile(): HasOne  { return $this->hasOne(LandlordProfile::class); }
    public function trustScore(): HasOne       { return $this->hasOne(TrustScore::class); }

    // ---- Landlord-owned things ----
    public function boardingHouses(): HasMany  { return $this->hasMany(BoardingHouse::class, 'landlord_id'); }

    // ---- Renter activity ----
    public function applications(): HasMany    { return $this->hasMany(RentalApplication::class, 'renter_id'); }
    public function tenancies(): HasMany       { return $this->hasMany(Tenancy::class, 'renter_id'); }
    public function favorites(): HasMany       { return $this->hasMany(Favorite::class); }
    public function reviews(): HasMany         { return $this->hasMany(Review::class, 'renter_id'); }

    // ---- Trust score ----
    public function trustScoreEvents(): HasMany { return $this->hasMany(TrustScoreEvent::class); }
    public function disputes(): HasMany          { return $this->hasMany(Dispute::class); }

    // ---- Hard-delete policy ----
    public function canBeHardDeleted(): bool
    {
        return $this->applications()->count() === 0
            && $this->tenancies()->count() === 0
            && $this->boardingHouses()->count() === 0;
    }

    // ---- Auth lockout helpers ----
    public function isLockedOut(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }
    public function activeTenancy(): ?\App\Models\Tenancy
    {
        return $this->tenancies()
            ->where('status', \App\Enums\TenancyStatus::Active)
            ->first();
    }

    public function hasActiveTenancy(): bool
    {
        return $this->activeTenancy() !== null;
    }
}