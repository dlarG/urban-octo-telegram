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
        'accepted_at', 'rejected_at', 'documents_submitted_at',
        'validated_by',
    ];

    protected $casts = [
        'approval_status'         => LandlordApprovalStatus::class,
        'accepted_at'             => 'datetime',
        'rejected_at'             => 'datetime',
        'documents_submitted_at' => 'datetime',
    ];

    public function hasSubmittedDocuments(): bool
    {
        return ! is_null($this->documents_submitted_at)
            && $this->valid_id_path
            && $this->business_permit_path;
    }

    public function canListProperties(): bool
    {
        return $this->hasSubmittedDocuments()
            && $this->approval_status === LandlordApprovalStatus::Accepted;
    }

    public function user(): BelongsTo        { return $this->belongsTo(User::class); }
    public function validator(): BelongsTo   { return $this->belongsTo(User::class, 'validated_by'); }

    public function isAccepted(): bool { return $this->approval_status === LandlordApprovalStatus::Accepted; }
    public function isPending(): bool  { return $this->approval_status === LandlordApprovalStatus::Pending; }
    public function isRejected(): bool { return $this->approval_status === LandlordApprovalStatus::Rejected; }
}