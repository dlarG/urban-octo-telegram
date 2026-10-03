<?php
namespace App\Models;

use App\Enums\LandlordApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'business_name', 'gcash_number', 'maya_number',
        'valid_id_path', 'business_permit_path',
        'approval_status', 'rejection_reason',
        'accepted_at', 'rejected_at', 'onboarding_completed_at',
        'validated_by',
    ];

    protected $casts = [
        'approval_status'         => LandlordApprovalStatus::class,
        'accepted_at'             => 'datetime',
        'rejected_at'             => 'datetime',
        'onboarding_completed_at' => 'datetime',
    ];

    public function isOnboardingComplete(): bool
    {
        return ! is_null($this->onboarding_completed_at);
    }

    public function canListProperties(): bool
    {
        return $this->isOnboardingComplete()
            && $this->approval_status === LandlordApprovalStatus::Accepted;
    }

    public function user(): BelongsTo        { return $this->belongsTo(User::class); }
    public function validator(): BelongsTo   { return $this->belongsTo(User::class, 'validated_by'); }

    public function isAccepted(): bool { return $this->approval_status === LandlordApprovalStatus::Accepted; }
    public function isPending(): bool  { return $this->approval_status === LandlordApprovalStatus::Pending; }
    public function isRejected(): bool { return $this->approval_status === LandlordApprovalStatus::Rejected; }
}