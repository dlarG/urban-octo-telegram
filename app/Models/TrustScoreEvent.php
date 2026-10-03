<?php
namespace App\Models;

use App\Enums\TrustEventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustScoreEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tenancy_id', 'payment_id', 'dispute_id',
        'event_type', 'delta', 'score_after', 'reason', 'created_by',
    ];

    protected $casts = [
        'event_type'  => TrustEventType::class,
        'delta'       => 'decimal:2',
        'score_after' => 'decimal:2',
    ];

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function tenancy(): BelongsTo  { return $this->belongsTo(Tenancy::class); }
    public function payment(): BelongsTo  { return $this->belongsTo(Payment::class); }
    public function dispute(): BelongsTo  { return $this->belongsTo(Dispute::class); }
    public function author(): BelongsTo   { return $this->belongsTo(User::class, 'created_by'); }

    // Ledger is append-only. Block updates and deletes at the model layer.
    protected static function booted(): void
    {
        static::updating(fn() => throw new \DomainException('Trust score events are append-only.'));
        static::deleting(fn() => throw new \DomainException('Trust score events are append-only.'));
    }
}